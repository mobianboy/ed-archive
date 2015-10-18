<?php
namespace Eardish\Bridge\Controllers;

use Eardish\Bridge\Agents\AnalyticsAgent;
use Eardish\Bridge\Agents\ImageProcessingAgent;
use Eardish\Bridge\Agents\ProfileAgent;
use Eardish\Bridge\Agents\MusicAgent;
use Eardish\Bridge\Agents\RecommendationAgent;
use Eardish\Bridge\Agents\UserAgent;
use Eardish\Bridge\Controllers\Core\AbstractController;

class ProfileController extends AbstractController
{
    protected $profileAgent;
    protected $musicAgent;
    protected $imageProcessingAgent;
    protected $userAgent;
    protected $recommendationAgent;
    protected $analyticsAgent;
    protected $requestId;

    public function __construct(ProfileAgent $profileAgent, MusicAgent $musicAgent, ImageProcessingAgent $imageProcessingAgent, UserAgent $userAgent, RecommendationAgent $recommendationAgent, AnalyticsAgent $analyticsAgent)
    {
        $this->profileAgent = $profileAgent;
        $this->musicAgent = $musicAgent;
        $this->imageProcessingAgent = $imageProcessingAgent;
        $this->userAgent = $userAgent;
        $this->recommendationAgent = $recommendationAgent;
        $this->analyticsAgent = $analyticsAgent;
    }

    public function artCreate($requestId)
    {
        $self = $this;
        $this->kernel->setRequests([
            // pull out image data from request, then send to image processing for processing.
            function() use ($self, $requestId) {
                $this->checkAccess($this->dataBlock['profileId']);
                $profileId = $this->checkOptional('profileId', $this->dataBlock);
                $self->kernel->setVariable($requestId, "profileId", $profileId);
                $type = $self->checkOptional('type', $this->dataBlock);
                $self->kernel->setVariable($requestId, "type", $type);
                $title = $self->checkOptional('title', $this->dataBlock, 'no title');
                $description = $self->checkOptional('description', $this->dataBlock, 'no description');
                $imageLocation = $self->checkOptional('url', $this->dataBlock, 'default');
                $self->imageProcessingAgent->addArt($profileId, $title, $type, $imageLocation, $description, $requestId);
            },
            // Check to see if everything happened successfully
            function($response, $previousIndex) use ($self, $requestId) {
                if ($this->kernel->getVariable($requestId, "type") == 'avatar') {
                    $profileUpdate = ['id' => $this->kernel->getVariable($requestId, "profileId"),'art_id' => $response['data'][$previousIndex]['artId']];
                    $self->profileAgent->editArtistProfile($profileUpdate, $requestId);
                }
                if (isset($response['data'][0]['artId'])) {
                    $self->kernel->setVariable($requestId, "artId", $response['data'][0]['artId']);
                } else {
                    $response = [];
                    $response['data'][$previousIndex]['success'] = false;

                }
                $response['requestId'] = $requestId;
                $self->kernel->next($response, true);
            },
            // get the art urls
            function($response, $previousIndex) use ($self, $requestId) {
                if (array_key_exists('success', $response['data'][$previousIndex]) && !$response['data'][$previousIndex]['success']) {
                    $this->reportError('art could not be saved');
                }
                $response['data'][$previousIndex]['artId'] = $self->kernel->getVariable($requestId, "artId");
                $this->data = $response['data'][$previousIndex];
                unset($response['data'][$previousIndex]['success']);

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * Only gets run if an image url other than default is given to the controller
     *
     * @return array
     * @param $requestId int
     */
    private function addArt($requestId)
    {
        $profileId = $this->checkOptional('id', $this->dataBlock);
        if (!$profileId) {
            $profileId = $this->checkOptional('profileId', $this->dataBlock);
        }
        $this->checkAccess($profileId);

        $type = $this->checkOptional('type', $this->dataBlock['art']);
        $title = $this->checkOptional('title', $this->dataBlock['art'], 'untitled.jpg');
        $description = $this->checkOptional('description', $this->dataBlock['art'], 'no description');
        $imageLocation = $this->checkOptional('url', $this->dataBlock['art'], 'default');
        $this->imageProcessingAgent->addArt($profileId, $title, $type, $imageLocation, $description, $requestId);
    }

    public function processArtResponse($response, $previousIndex) {
        if (array_key_exists('success', $response['data'][$previousIndex]) && !$response['data'][$previousIndex]['success']) {
             $this->reportError('art could not be saved');
        }
        unset($response['data'][$previousIndex]['success']);

        return $response['data'][$previousIndex];
    }

    public function selectProfile($requestId)
    {
        $this->kernel->setRequests([
            // select profile
            function () use ($requestId) {
                $profileId = $this->dataBlock['id'];
                $this->kernel->setVariable($requestId, 'profileId', $profileId);
                $this->profileAgent->selectProfile($profileId, $requestId);
            },
            // get profile badges
            function ($response, $previousIndex) use ($requestId) {

                $profileResponseFlat = $response['data'][$previousIndex][0];

                $profile = $this->formatProfileResponse($profileResponseFlat);

                if (!$profile) {
                    $this->reportError('no profile data returned');
                }
                $this->data = $profile;

                $this->kernel->setVariable($requestId, 'profile', $profile);
                $profileId = $this->kernel->getVariable($requestId, 'profileId');
                $this->analyticsAgent->getProfileBadges($profileId, $requestId);
            },
            // get art urls
            function ($response, $previousIndex) use ($requestId) {
                unset($response['data'][$previousIndex]['success']);
                $badges = $this->formatBadges($response['data'][$previousIndex]);
                if (!$badges) {
                    $this->data['badges'] = [];
                } else {
                    $this->data['badges'] = $badges['badges'];
                }
                $profile = $this->kernel->getVariable($requestId, 'profile');
                $artId = $profile['art_id'];

                if (!$artId) {
                    $this->reportError('could not fetch art urls');
                }
                $this->profileAgent->getArtUrls($artId, $requestId);
            },
            // get artist genre
            function ($response, $previousIndex) use ($requestId) {
                $artResponse = $response['data'][$previousIndex];

                if ($artResponse) {
                    $this->createArtMap($artResponse);
                } else {
                    $this->data['art'] = [];
                }
                $profile = $this->kernel->getVariable($requestId, 'profile');

                if ($profile['type'] != 'fan') {
                    $this->musicAgent->getArtistGenre($this->kernel->getVariable($requestId, 'profileId'), $requestId);
                } else {
                    $this->kernel->next(["requestId" => $requestId], true);
                }
            },
            // return data
            function ($response, $previousIndex) use ($requestId) {
                $genreResponse = $response['data'][$previousIndex];
                if ($genreResponse) {
                    $this->data['genre_id'] = $genreResponse['genre_id'];
                }
                $profile = $this->kernel->getVariable($requestId, 'profile');
                $this->setModelType("profile-" . $profile['type']);

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * @return array
     */
    public function listArtistProfiles($requestId)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $this->profileAgent->listArtistProfiles($requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                $profiles = $response['data'][$previousIndex];
                if (!count($profiles)) {
                    $this->reportError('profiles could not be returned');
                }
                $this->data = $profiles;
                $this->listType = "artistProfiles";
                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * @return array
     */
    public function editArtistProfile($requestId)
    {
        // Check to see if the currently logged in user has access. For editProfile, this includes checking to see whether the
        // currently logged in user is an ar-rep that created the given artist they are trying to edit.
        $this->checkAccess($this->dataBlock['id'], $this->dataBlock['ar_rep']);

        $self = $this;

        $this->kernel->setRequests([
            function() use ($self, $requestId) {
                if (isset($this->dataBlock['ar_rep'])) {
                    unset($this->dataBlock['ar_rep']);
                }
                $profileId = $this->dataBlock['id'];
                $clientProfileData = $this->getClientProfileData($this->dataBlock);
                $clientProfileData['data']['profile']['id'] = $profileId;
                $clientProfileData['data']['profile']['last_edited_by'] = $this->metaBlock->getCurrentProfile();
                //set art id if art is uploaded
                $self->kernel->setVariable($requestId, "clientProfileData", $clientProfileData);
                $self->kernel->setVariable($requestId, "profileId", $profileId);
                if (!empty($clientProfileData['data']['art'])) {
                    $self->addArt($requestId);
                } else {
                    $self->kernel->next(['requestId' => $requestId], true);
                }
            },
            function ($response, $previousIndex) use ($self, $requestId) {
                $clientProfileData = $self->kernel->getVariable($requestId, "clientProfileData");
                if ($response['data'][$previousIndex]) {
                    $artResponse = $this->processArtResponse($response, $previousIndex);
//                    $clientProfileData['data']['profile']['art_id'] = $response['data'][$previousIndex]['artId'];
                    $clientProfileData['data']['profile']['art_id'] = $artResponse['artId'];
                }
                $genreId = $clientProfileData['data']['profile_genre']['genre_id'];
                $this->kernel->setVariable($requestId, 'genreId', $genreId);
                $profileId = $self->kernel->getVariable($requestId, "profileId");
                $self->kernel->setVariable($requestId, "clientProfileData", $clientProfileData);
                $this->musicAgent->getArtistGenre($profileId, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                $genreId = $this->kernel->getVariable($requestId, 'genreId');
                $oldGenreId = $response['data'][$previousIndex]['genre_id'];
                if ($oldGenreId != $genreId) {
                    $profileId = $this->kernel->getVariable($requestId, "profileId");
                    $this->musicAgent->updateArtistGenre($profileId, $genreId, $requestId);
                } else {
                    $this->kernel->next(['requestId' => $requestId], true);
                }
            },
            function($response, $previousIndex) use ($requestId) {
                if ($response['data'][$previousIndex]['id']) {
                    $genreId = $this->kernel->getVariable($requestId, 'genreId');
                    $profileId = $this->kernel->getVariable($requestId, "profileId");
                    $this->musicAgent->updateAllGenreTracks($profileId, $genreId, $requestId);
                } else {
                    $this->kernel->next(['requestId' =>$requestId], true);
                }
            },
            function ($response, $previousIndex) use ($self, $requestId) {
                $clientProfileData = $self->kernel->getVariable($requestId, "clientProfileData");
                $this->profileAgent->editArtistProfile($clientProfileData['data']['profile'], $requestId);
            },
            function ($response, $previousIndex) use ($self, $requestId) {
                if (!count($response['data'][$previousIndex])) {
                    $this->reportError('profile could not be updated');
                }
                $this->data = $this->formatProfileResponse($response['data'][$previousIndex]);
                $self->kernel->setVariable($requestId, "profile", $this->data);
                $this->kernel->setVariable($response, ' contactId', $response['data'][$previousIndex]['contact_id']);
                $profileId = $self->kernel->getVariable($requestId, "profileId");
                $this->analyticsAgent->getProfileBadges($profileId, $requestId);
            },
            function ($response, $previousIndex) use ($self, $requestId) {
                unset($response['data'][$previousIndex]['success']);
                $badges = $this->formatBadges($response['data'][$previousIndex]);
                if (!$badges) {
                    $this->data['badges'] = [];
                } else {
                    $this->data['badges'] = $badges['badges'];
                }
                $clientProfileData = $self->kernel->getVariable($requestId, "clientProfileData");
                $hasContactData = $this->hasContactData($clientProfileData['data']['contact']);
                $self->kernel->setVariable($requestId, "hasContactData", $hasContactData);
                if ($hasContactData) {
                    $this->profileAgent->editContactInfo($clientProfileData['data']['contact'], $requestId);
                } else {
                    $profileId = $this->kernel->getVariable($requestId, 'profileId');
                    $this->profileAgent->selectContactInfo($profileId, $requestId);
                }
            },
            function ($response, $previousIndex) use ($self, $requestId) {
                $hasContactData = $self->kernel->getVariable($requestId, "hasContactData");
                if ($hasContactData && !$response['data'][$previousIndex]) {
                    $this->reportError('contact information could not be updated');
                } else if ($response['data'][$previousIndex]) {
                    $contactUpdate = $this->formatProfileResponse($response['data'][$previousIndex], true);
                    $this->data['address'] = $contactUpdate['address'];
                }
                $profile = $self->kernel->getVariable($requestId, "profile");
                $badges = $response['data'][$previousIndex];
                if (!$badges) {
                    $profile['badges'] = [];
                } else {
                    $profile['badges'] = $badges;
                }
                $self->kernel->setVariable($requestId, "profile", $profile);
                if ($profile['art_id']) {
                    $artId = $profile['art_id'];
                    $self->kernel->setVariable($requestId, "profile", $profile);
                    $self->profileAgent->getArtUrls($artId, $requestId);
                } else {
                    $self->data['art'] = [];
                    $response = [];
                    $response['requestId'] = $requestId;
                    $self->kernel->next($response, true);
                }
                $self->kernel->setVariable($requestId, "profile", $profile);
            },
            function ($response, $previousIndex) use ($self, $requestId) {
                $profile = $self->kernel->getVariable($requestId, "profile");
                $artResponse = $response['data'][$previousIndex];
                if ($artResponse) {
                    $self->createArtMap($artResponse);
                } else {
                    $self->data['art'] = [];
                }

                $this->modelType = "profile-".$profile['type'];

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * returns artist track art and tracks (for AR tools)
     * @return array
     */
    public function getArtistContent($requestId)
    {
        $this->checkAccess($this->dataBlock['id']);

        $this->kernel->setRequests([
            function() use ($requestId) {
                $profileId = $this->dataBlock['id'];
                $this->profileAgent->getArtistContent($profileId, $requestId);
            },
            function ($response, $previousIndex) {
                if (!count($response['data'][$previousIndex])) {
                    $this->reportError('could not return artist content or profile does not have any media.');
                }

                $this->data = $response['data'][$previousIndex];
                $this->listType = "artistContent";

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * @return array
     * @param $requestId int
     */
    public function createArtistProfile($requestId)
    {
        // Create a new user account for the artist. Using a default password
        $this->kernel->setRequests([
            function() use ($requestId) {
                $email = $this->dataBlock['email'];
                $this->kernel->setVariable($requestId, 'email', $email);
               $this->userAgent->checkIfEmailAlreadyExists($email, $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                //Make sure email hasn't been used before creating user
                if ($response['data'][$previousIndex]['email-exists']) {
                    $this->reportError('could not create user. email already exists.');
                }
                $this->userAgent->createUser($this->kernel->getVariable($requestId, 'email'), 'd3fault_Passw0rd', $requestId);
            },
            // insert data into contact table
            function($response, $previousIndex) use ($requestId) {
                $userId = $response['data'][$previousIndex]['userId'];
                if (!$userId) {
                    $this->reportError('user could not be created');
                }
                $this->kernel->setVariable($requestId, 'userId', $userId);
                $profileData = $this->getClientProfileData($this->dataBlock);
                $this->kernel->setVariable($requestId, 'profileData', $profileData);
                if ($this->hasContactData($profileData['data']['contact'])) {
                    $this->profileAgent->createContactInfo($profileData['data']['contact'], $requestId);
                } else {
                    $this->kernel->next(['requestId' => $requestId], true);
                }
            },
            // return contact id to be inserted with profile data
            function ($response, $previousIndex) use ($requestId) {
                $profileData = $this->kernel->getVariable($requestId, 'profileData');
                if (!$response['data'][$previousIndex] && $this->hasContactData($profileData['data']['contact'])) {
                    $this->reportError('contact data could not be saved.');
                } else {
                    $contactId = $response['data'][$previousIndex]['contactId'];
                    $profileData['data']['profile']['contact_id'] = intval($contactId);
                }
                $userId = $this->kernel->getVariable($requestId, 'userId');
                $profileData['data']['profile']['invite_code'] = $this->dataBlock['inviteCode'];
                $profileData['data']['profile']['user_id'] = intval($userId);
                // Special case for ar-admins registering. flanqui!!a is the secret invite code to be an admin.
                $profileData['data']['profile']['type'] = $this->dataBlock['type'];
                $profileData['data']['profile']['last_edited_by'] = $this->metaBlock->getCurrentProfile();

                $this->profileAgent->createProfile($profileData['data']['profile'], $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                $profileResponse = $response['data'][$previousIndex];
                $profile = $this->formatProfileResponse($profileResponse);
                $profileId = $profile['id'];
                $this->kernel->setVariable($requestId, 'profileId', $profileId);
                $this->kernel->setVariable($requestId, 'profile', $profile);
                if (!$profileId) {
                    $this->reportError('profile could not be created');
                }
                $profileData = $this->kernel->getVariable($requestId, 'profileData');
                $genreId = $profileData['data']['profile_genre']['genre_id'];

                $this->musicAgent->setArtistGenre($profileId, $genreId, $requestId);

            },
            function ($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('genre could not be set for profile');
                }
                $url = $this->checkOptional('url', $this->dataBlock['art']);
                if ($url == "default" || $url == "") {
                    $profileData['data']['profile']['art_id'] = $artId = 1;
                    $this->kernel->next(['requestId' => $requestId], true);
                } else {
                    $profileId = $this->kernel->getVariable($requestId, 'profileId');
                    $this->dataBlock['id'] = $profileId;
                    $this->addArt($requestId);
                }
            },
            // add art id to the profile if profile pic has been uploaded (edit profile)
            function($response, $previousIndex) use ($requestId) {

                if ($response['data'][$previousIndex]) {

                    $artResponse = $this->processArtResponse($response, $previousIndex);
                    $artId = $artResponse['artId'];
                } else {
                    $artId = 0;
                }
                if ($artId != 0 && $artId != []) {
                    $profileId = $this->kernel->getVariable($requestId, 'profileId');
                    $updateArt['data']['profile']['id'] = intval($profileId);
                    $updateArt['data']['profile']['art_id'] = $artId;
                    $this->profileAgent->editArtistProfile($updateArt['data']['profile'], $requestId);
                } else {
                    $this->kernel->next(['requestId' => $requestId], true);
                }
            },
            function($response, $previousIndex) use ($requestId) {
                if (!($response['data'][$previousIndex] == array()) && !$response['data'][$previousIndex]['id']) {
                    $this->reportError('profile art could not be created');
                } else {
                    $profile = $this->kernel->getVariable($requestId, 'profile');
                    $artId = $response['data'][$previousIndex]['art_id'];
                    $profile['art_id'] = $artId;
                }
                $profileId = $this->kernel->getVariable($requestId, 'profileId');
                $this->musicAgent->createAlbum($profileId, 'initial_uploads', $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('initial uploads album could not be created');
                }
                $profile = $this->kernel->getVariable($requestId, 'profile');
                $this->data = $profile;
                $this->modelType = "profile-".$profile['type'];

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    /**
     * @return array
     */
    public function modifyProfileGenreBlend($requestId)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $liked = $this->dataBlock['genresLiked'];
                $disliked = $this->dataBlock['genresDisliked'];
                $genreIds = $this->buildGenreArray($liked, $disliked);
                $profileId = $this->metaBlock->getCurrentProfile();

                $this->recommendationAgent->modifyProfileGenreBlend($profileId, $genreIds, $requestId);

            },
            function($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('genre blend could not be modified.');
                }
                $this->data = $response['data'][$previousIndex];
                $this->modelType = 'genre-blend';
                $profileId = $this->metaBlock->getCurrentProfile();
                $this->profileAgent->updateProfileIsOnboarded($profileId, $requestId);
            },
            function($response, $previousIndex) use ($requestId) {
                if (!is_bool($response['data'][$previousIndex])) {
                    $response['onboarded'] = false;
                    $this->reportError('user has not been onboarded yet');
                } else {
                    $response['onboarded'] = true;
                }

                return $this->reportSuccess();
            }
        ], $requestId);

       $this->kernel->first($requestId);
    }

    public function getProfileGenreBlend($requestId)
    {
        $self = $this;

        $this->kernel->setRequests([
            // pull out image data from request, then send to image processing for processing.
            function() use ($self, $requestId) {
                $this->recommendationAgent->getProfileGenreBlend($this->metaBlock->getCurrentProfile(), $requestId);
            },
            // Check to see if everything happened successfully
            function($response, $previousIndex) use ($self, $requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError('genre blend could not be returned.');
                }
                $self->data = $response['data'][$previousIndex];
                $self->modelType = 'genre-blend';

                return $self->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    private function buildGenreArray($liked, $disliked)
    {
        $genreIds = [];

        for ($i = 0; $i < count($liked); $i++) {
            $genreIds[$liked[$i]] = 2;
        }
        for ($i = 0; $i < count($disliked); $i++) {
            $genreIds[$disliked[$i]] = 0;
        }

        return $genreIds;
    }
}
