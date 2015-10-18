<?php
namespace Eardish\Bridge\Controllers;

use Eardish\Bridge\Agents\AnalyticsAgent;
use Eardish\Bridge\Agents\ImageProcessingAgent;
use Eardish\Bridge\Agents\MusicAgent;
use Eardish\Bridge\Agents\EmailAgent;
use Eardish\Bridge\Agents\UserAgent;
use Eardish\Bridge\Agents\RecommendationAgent;
use Eardish\Bridge\Agents\AuthAgent;
use Eardish\Bridge\Agents\ProfileAgent;
use Eardish\Bridge\Controllers\Core\AbstractController;

class UserController extends AbstractController
{
    const BASE_INVITE_LIMIT = 5;
    protected $musicAgent;
    protected $socialAgent;
    protected $photoAgent;
    protected $playlistAgent;
    protected $emailAgent;
    protected $userAgent;
    protected $recommendationAgent;
    protected $imageProcessingAgent;
    protected $authAgent;
    protected $profileAgent;
    protected $analyticsAgent;

    public function __construct(MusicAgent $musicAgent, UserAgent $userAgent, EmailAgent $emailAgent, RecommendationAgent $recommendationAgent, AuthAgent $authAgent, ImageProcessingAgent $imageProcessingAgent, ProfileAgent $profileAgent, AnalyticsAgent $analyticsAgent)
    {
        $this->musicAgent = $musicAgent;
        $this->userAgent = $userAgent;
        $this->emailAgent = $emailAgent;
        $this->recommendationAgent = $recommendationAgent;
        $this->authAgent = $authAgent;
        $this->imageProcessingAgent = $imageProcessingAgent;
        $this->profileAgent = $profileAgent;
        $this->analyticsAgent = $analyticsAgent;
    }

    public function inviteUser($requestId)
    {
        $currentUser = intval($this->metaBlock->getCurrentUser());
        $profileId = intval($this->metaBlock->getCurrentProfile());
        //Count from invite table
        $this->kernel->setRequests([
            function() use ($currentUser, $requestId) {
                $this->userAgent->invitesUsed($currentUser, $requestId); //numInvites
            },
            function($response, $previousIndex) use ($currentUser, $requestId) {
                $this->kernel->setVariable($requestId, 'invitesUsed', $response['data'][$previousIndex]['invitesUsed']);
                $this->userAgent->extraInvites($currentUser, $requestId); //$extraInvites
            },
            function($response, $previousIndex) use ($currentUser, $requestId) {
                if ($response['data'][$previousIndex]['extraInvites']) {
                    $extraInvites = $response['data'][$previousIndex]['extraInvites'];
                } else {
                    $extraInvites = 0;
                }
                $this->kernel->setVariable($requestId, 'extraInvites', $extraInvites);
                $invitesUsed = $this->kernel->getVariable($requestId, 'invitesUsed');
                //Base invite limit is currently set to 5
                //Case 1: User has not passed their 5 base limit
                if (intval($invitesUsed < self::BASE_INVITE_LIMIT)) {
                    $invitesRemaining = self::BASE_INVITE_LIMIT - ($invitesUsed + 1);
                    $this->kernel->setVariable($requestId, 'invitesRemaining', $invitesRemaining);
                    $this->kernel->next(["requestId" => $requestId], true);
                }
                $this->kernel->setVariable($requestId, 'updateEIbool', false);
                //Case 2: User has passed their base invite limit (if an ar-admin, they can invite unlimited people and it will use code flanqui!!a)
                if ($invitesUsed >= self::BASE_INVITE_LIMIT && $this->metaBlock->getProfileType() != 'ar-admin' ) {
                    $invitesRemaining = 0;
                    $this->kernel->setVariable($requestId, 'invitesRemaining', $invitesRemaining);
                    //User has passed base limit and no longer has invites
                    if (!$extraInvites) {
                        $this->reportError('no invites available');
                    }
                    //User has passed base limit but has extra invites left
                    if ($extraInvites > 0) {
                        $this->kernel->setVariable($requestId, 'updateEIbool', true);
                        $this->userAgent->updateExtraInvites($currentUser, $extraInvites - 1, $requestId);
                    }
                } else {
                    $this->kernel->next(["requestId" => $requestId], true);
                }
            },
            function($response, $previousIndex) use ($currentUser, $profileId, $requestId) {
                if ($this->kernel->getVariable($requestId, 'updateEIbool')) {
                    $extraInvites = $response['data'][$previousIndex]['extraInvites'];
                    $this->kernel->setVariable($requestId, 'extraInvites', $extraInvites);
                }
                $this->profileAgent->selectProfile($profileId, $requestId); //profileResponse
            },
            function($response, $previousIndex) use ($currentUser, $requestId) {
                $profileResponse = $response['data'][$previousIndex][0];
                if (!$profileResponse) {
                    $this->reportError('could not get profile name for email');
                }
                $fullName = $profileResponse['first_name'] . " " . $profileResponse['last_name'];
                $this->kernel->setVariable($requestId, 'fullName', $fullName);
                $this->userAgent->createInviteCode($requestId); //inviteCode
            },
            function($response, $previousIndex) use ($currentUser, $requestId) {
                $inviteCode = $response['data'][$previousIndex]['inviteCode'];
                $this->kernel->setVariable($requestId, 'inviteCode', $inviteCode);
                if ($inviteCode) {
                    $toAddress = $this->dataBlock['email'];
                    $fullName = $this->kernel->getVariable($requestId, 'fullName');
                    if ($this->dataBlock['admin']) {
                        $inviteCode = "flanqui!!a";
                        $this->kernel->setVariable($requestId, 'inviteCode', $inviteCode);
                    }
                    $this->emailAgent->sendInviteCode(array($toAddress), $inviteCode, $fullName, $requestId);
                } else {
                    $this->reportError('invite code failed');
                }
            },
            function($response, $previousIndex) use ($currentUser, $requestId) {
                $inviteCode = $this->kernel->getVariable($requestId, 'inviteCode');
                $toAddress = $this->dataBlock['email'];
                $emailResponse = $response["data"][$previousIndex]['email'];
                if ($emailResponse) {
                    // add userID and code to invite_code table (removing one of that user's invites)
                    $this->userAgent->registerInviteCode($currentUser, $inviteCode, $toAddress, $requestId); //
                } else {
                    $this->reportError('email failed');
                }
            },
            function($response, $previousIndex) use ($currentUser, $requestId) {
                $registerInviteCode = $response['data'][$previousIndex]['registerInviteCode'];
                if (!$registerInviteCode) {
                    $this->reportError('code not registered');
                }
                $invitesRemaining = $this->kernel->getVariable($requestId, 'invitesRemaining');
                $extraInvites = $this->kernel->getVariable($requestId, 'extraInvites');
                $this->data['referralsRemaining'] = $invitesRemaining + $extraInvites;
                $this->data['inviteCode'] = $this->kernel->getVariable($requestId, 'inviteCode');
                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * @param $requestId
     * @return array|void
     * @throws \Exception
     */
    public function newUser($requestId)
    {
        $email = $this->dataBlock['email'];
        $email = strtolower($email);

        $this->kernel->setRequests([
            function() use ($requestId, $email) {
                //TESTING with fake invite code
                $this->kernel->setVariable($requestId, 'email', $email);
                $inviteCode = $this->dataBlock['inviteCode'];
                if (strtolower($inviteCode) == 'laurel' || strtolower($inviteCode) == 'flanqui!!a'){
                    $this->kernel->setVariable($requestId, 'admin', true);
                    $this->kernel->setVariable($requestId, 'validationInviteCode', true);
                    $this->kernel->next(['requestId'=> $requestId], true);
                } else {
                    $this->kernel->setVariable($requestId, 'admin', false);
                    $this->userAgent->validateInviteCode($inviteCode, $requestId);
                }
            },
            function($response, $previousIndex) use ($requestId) {
                // if not admin (laurel) set the validation variable with the response from the db
                $validationInviteCode = $this->kernel->getVariable($requestId, 'validationInviteCode');
                if (!$validationInviteCode) {
                    $validationInviteCode = $response['data'][$previousIndex];
                    $this->kernel->setVariable($requestId, 'validationInviteCode', $validationInviteCode);
                }
                $password = $this->dataBlock['password'];
                if (!$validationInviteCode) {
                    $this->addInvalidField("inviteCode", "invalid");
                }
                $this->authAgent->hashPass($password, $requestId);
            },

            function ($response, $previousIndex) use ($requestId) {
                $hashPass = $response['data'][$previousIndex]['hashPass'];
                if (!$hashPass) {
                    $this->reportError('could not hash password');
                }
                $this->kernel->setVariable($requestId, 'hashPass', $hashPass);
                $email = $this->kernel->getVariable($requestId, 'email');
                //Make sure email hasn't been used before creating user
                $this->userAgent->checkIfEmailAlreadyExists($email, $requestId);
            },

            function ($response, $previousIndex) use ($requestId) {
                if ($response['data'][$previousIndex]['email-exists']) {
                    $this->addInvalidField("email", "taken");
                }
                //find all form errors and report back to client if any exist
                if ($this->metaBlock->getInvalidFields()) {
                    $this->reportError('email taken or invite code not valid');
                }
                $hashPass = $this->kernel->getVariable($requestId, 'hashPass');
                $email = $this->kernel->getVariable($requestId, 'email');
                $this->userAgent->createUser($email, $hashPass, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                $userId = $response['data'][$previousIndex]['userId'];
                $this->data['userId'] = $userId;
                $this->kernel->setVariable($requestId, 'userId', $userId);
                if (!$userId) {
                    $this->reportError('user could not be created');
                }
                $validationInviteCode = $this->kernel->getVariable($requestId, 'validationInviteCode');
                if (empty($validationInviteCode['artist_name'])) {
                    $inviteId = $validationInviteCode['id'];
                    $this->kernel->setVariable($requestId, 'inviteId', $inviteId);
                    $this->kernel->next(['requestId' => $requestId], true);
                } else {
                    $inviterId = $validationInviteCode['inviter_id'];
                    $this->kernel->setVariable($requestId, 'inviterId', $inviterId);
                    $inviteCode = $this->kernel->getVariable($requestId, 'inviteCode');
                    $email = $this->kernel->getVariable($requestId, 'email');
                    $this->userAgent->registerInviteCode($inviterId, $inviteCode, $email, $requestId);
                }
            },
            function ($response, $previousIndex) use ($requestId) {
                $validationInviteCode = $this->kernel->getVariable($requestId, 'validationInviteCode');
                if (!empty($validationInviteCode['artist_name'])) {
                    $inviteId = $response['data'][$previousIndex]['id'];
                    $this->kernel->setVariable($requestId, 'inviteId', $inviteId);
                }
                $profileData = $this->getClientProfileData($this->dataBlock);

                $this->kernel->setVariable($requestId, 'profileData', $profileData);
                if ($this->hasContactData($profileData['data'])) {
                    $this->profileAgent->createContactInfo($profileData['data']['contact'], $requestId);
                } else {
                    $this->kernel->next(['requestId' => $requestId], true);
                }
            },
            function ($response, $previousIndex) use ($requestId) {
            // insert data into contact table, and return contact id to be inserted with profile data
                $profileData = $this->kernel->getVariable($requestId, 'profileData');
                if (!empty($response['data'][$previousIndex])) {
                    //will be empty if called by next
                    $contactId = $response['data'][$previousIndex]['contactId'];
                    if (!$contactId && $this->hasContactData($profileData['data'])) {
                        $this->reportError('could not save contact data');
                    } elseif ($contactId && $this->hasContactData($profileData['data'])) {
                        $profileData['data']['profile']['contact_id'] = intval($contactId);
                    }
                }
                $userId = $this->kernel->getVariable($requestId, 'userId');
                $profileData['data']['profile']['art_id'] = 1;
                $profileData['data']['profile']['user_id'] = intval($userId);
                if (!isset($profileData['data']['profile']['type'])) {
                    $this->dataBlock['type'] = 'fan';
                }
                if ($this->dataBlock['inviteCode'] == "flanqui!!a") {
                    $this->dataBlock['type'] = "ar-admin";
                }
                $profileData['data']['profile']['type'] = $this->dataBlock['type'];
                // dont submit the inviteCode into fan profiles
                unset($profileData['data']['profile']['invite_code']);

                $this->profileAgent->createProfile($profileData['data']['profile'], $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                $profileId = $response['data'][$previousIndex]['id'];
                $this->data['profileId'] = $profileId;
                if (!$profileId) {
                    $this->reportError('profile could not be saved');
                }else {
                    $userId = $this->kernel->getVariable($requestId, 'userId');
                    $inviteId = $this->kernel->getVariable($requestId, 'inviteId');
                    if (!$this->kernel->getVariable($requestId, 'admin')) {
                        $this->userAgent->redeemInviteCode(intval($userId), intval($inviteId), $requestId);
                    } else {
                        $this->kernel->next(['requestId' => $requestId], true);
                    }
                }
            },
            function ($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex] && !$this->kernel->getVariable($requestId, 'admin')) {
                    $this->reportError('could not redeem invite code');
                }

                return $this->reportSuccess();
            }

        ], $requestId);

        $this->kernel->first($requestId);
    }

    public function referralsRemaining($requestId)
    {
        $currentUser = intval($this->metaBlock->getCurrentUser());

        $this->kernel->setRequests([
            function() use($currentUser, $requestId) {
                //count from invite table
                $this->userAgent->invitesUsed($currentUser, $requestId);
            },
            function($response, $previousIndex) use($currentUser, $requestId) {
                $invitesUsed = intval($response['data'][$previousIndex]['invitesUsed']);

                $this->kernel->setVariable($requestId, 'invitesUsed', $invitesUsed);
                //count of extra invites available
                $this->userAgent->extraInvites($currentUser, $requestId);
            },
            function($response, $previousIndex) use($currentUser, $requestId) {
                $extraInvitesAvailable = intval($response['data'][$previousIndex]['extraInvites']);
                $this->kernel->setVariable($requestId, 'extraInvitesAvailable', $extraInvitesAvailable);
                $extraInvitesAvailable = $this->kernel->getVariable($requestId, 'extraInvitesAvailable');

                $invitesUsed = $this->kernel->getVariable($requestId, 'invitesUsed');

                if ($invitesUsed < 5) {
                    $invitesRemaining = 4 - $invitesUsed;
                    $this->kernel->setVariable($requestId, 'invitesRemaining', $invitesRemaining);
                }

                if ($invitesUsed >= 5 && !$extraInvitesAvailable) {
                    $this->reportError('no invites available');
                }

                if ($invitesUsed >= 5 && $extraInvitesAvailable > 0) {
                    $this->userAgent->updateExtraInvites($currentUser, $extraInvitesAvailable - 1, $requestId);
                } else {
                    $this->kernel->next(["requestId" => $requestId], true);
                }
            },
            function($response, $previousIndex) use($currentUser, $requestId) {
                $extraInvitesAvailable = $this->kernel->getVariable($requestId, 'extraInvitesAvailable', $response['data'][$previousIndex]);
                $invitesRemaining = $this->kernel->getVariable($requestId, 'invitesRemaining');

                // +1 offsets numInvites, can be improved later but quick fix for now
                $this->data['referralsRemaining'] = $invitesRemaining + $extraInvitesAvailable + 1;

                return $this->reportSuccess();
            }
        ], $requestId
        );
        $this->kernel->first($requestId);
    }

    public function getUserStats($requestId)
    {
        $currentUser = intval($this->metaBlock->getCurrentUser());
        $profileId = intval($this->metaBlock->getCurrentProfile());
        $day = date('w');
        $weekStart = new \DateTime(date('Y-m-d', strtotime('-' . $day . ' days')));
        $weekStart = $weekStart->format('c');
        $weekEnd = new \DateTime(date('Y-m-d', strtotime('+' . (7 - $day) . ' days')));
        $weekEnd = $weekEnd->modify('-1 second');
        $weekEnd = $weekEnd->format('c');

        $this->kernel->setRequests([
            // get the track detail
            function() use ($currentUser, $weekStart, $weekEnd, $requestId) {
                $this->analyticsAgent->getCompletedListens($currentUser, "completedListen", $weekStart, $weekEnd, $requestId);
            },
            // get the track rating
            function($response, $previousIndex) use ($profileId, $weekStart, $weekEnd, $requestId) {
                $completedListens = null;

                $ratedTracks = $response['data'][$previousIndex]['count'];

                if (isset($ratedTracks ) && $ratedTracks ) {
                    $this->data['stats']['completedListens'] = $ratedTracks;
                } else {
                    $this->data['stats']['completedListens'] = 0;
                }
                $this->analyticsAgent->getRatedTracks($profileId, $weekStart, $weekEnd, $requestId);
            },
            function($response, $previousIndex) use ($currentUser, $profileId, $requestId) {
                $ratedTracks = $response['data'][$previousIndex]['count'];

                if (isset($ratedTracks) && $ratedTracks) {
                    $this->data['stats']['ratedTracks'] = $ratedTracks;
                } else {
                    $this->data['stats']['ratedTracks'] = 0;
                }
                $this->data['user_id'] = $currentUser;
                $this->data['profileId'] = $profileId;

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * @return array
     */
    public function forgotPassword($requestId)
    {
        $email = $this->dataBlock['email'];

        $this->kernel->setRequests([
            function() use ($requestId, $email) {
                $this->authAgent->generateResetPassCode($email, $requestId);
            },
            function($response, $previousIndex) use ($requestId, $email) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('could not generate reset passcode');
                }

                $resetPasscode = $response['data'][$previousIndex]['reset_passcode'];
                $this->kernel->setVariable($requestId, 'resetPasscode', $resetPasscode);

                $this->profileAgent->getFullNameByEmail($email, $requestId);
            },
            function($response, $previousIndex) use ($requestId, $email) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('could not return full name.');
                }

                $fullName = $response['data'][$previousIndex]['fullName'];
                $this->kernel->setVariable($requestId, 'fullName', $fullName);

                $resetPasscode = $this->kernel->getVariable($requestId, 'resetPasscode');

                $this->emailAgent->sendResetPassCode(array($email), $resetPasscode, $fullName, $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                $this->data['resetPasscode'] = $this->kernel->getVariable($requestId, 'resetPasscode');

                return $this->reportSuccess();
            }
        ],
            $requestId
        );
        $this->kernel->first($requestId);
    }

    /**
     * @return array
     */
    public function resetPassword($requestId)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $resetCode = $this->dataBlock['resetCode'];
                $this->authAgent->getEmailByResetCode($resetCode, $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('passcode is not associated with any email');
                }
                $email = $response['data'][$previousIndex]['email'];
                $this->kernel->setVariable($requestId, 'email', $email);

                $password = $this->dataBlock['password'];
                $dateNow = new \DateTime('now');
                $date = strtotime($dateNow->format('c'));
                $userExp = strtotime($response['data'][$previousIndex]['reset_passcode_exp']);

                if ($date > $userExp) {
                    $this->reportError('temporary passcode has expired');
                }

                $this->authAgent->hashPass($password, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                $email = $this->kernel->getVariable($requestId, 'email');
                $this->data['email'] = $email;
                $hashPass = $response['data'][$previousIndex]['hashPass'];
                //Replaces old password with new password and deletes temp reset code
                $this->authAgent->updatePassword($email, $hashPass, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('could not save new password');
                }
                // TODO need a message for client that passcode has expired
                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    public function getClientAws($requestId)
    {
        $this->kernel->setRequests(
            [
                function() use ($requestId) {
                    $this->authAgent->getClientAws($requestId);
                },
                function($response, $previousIndex) use ($requestId) {
                    $response = $response['data'][$previousIndex];
                    if (!$response) {
                        $this->reportError('Failed to load aws configuration');
                    }

                    $this->data = $response;

                    return $this->reportSuccess();
                }
            ],
            $requestId
        );

        $this->kernel->first($requestId);
    }
}
