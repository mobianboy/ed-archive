<?php
namespace Eardish\EmailService;

use Eardish\EmailService\Core\AbstractService;
use Eardish\EmailService\Core\Connection;
use Monolog\Logger;
use Twig_Autoloader;
use Twig_Loader_Filesystem;
use Twig_Environment;

require_once "vendor/twig/twig/lib/Twig/Autoloader.php";

class EmailService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Connection
     */
    protected $connectionService;

    /**
     * @var AWSService
     */
    protected $aws;

    public function __construct(Connection $connectionService, $kernel, $agentConfig)
    {
        parent::__construct($connectionService, $kernel, $agentConfig);
        $this->aws = new AWSService($agentConfig);
    }

    // sends an email to a user that forgot their password, provides temp password until they register a new one
    public function sendResetPassCode($emails, $newPassCode, $name, $serviceId)
    {
        $this->serviceKernel->register([
            function() use($emails, $newPassCode, $name, $serviceId) {
                Twig_Autoloader::register();

                $loader = new Twig_Loader_Filesystem(__DIR__.'/MailTemplates');
                $twig = new Twig_Environment($loader);

                $source = 'devdnr@eardish.com';
                $subject = 'Reset Your Password';
                $bodyText = $twig->render('resetPassword.txt', array(
                    'newPassCode' => $newPassCode,
                    'name' => $name
                ));
                $bodyHtml = $twig->render('resetPassword.mail.twig', array(
                    'newPassCode' => $newPassCode,
                    'name' => $name
                ));

                $response = $this->aws->sendEmail($emails, $source, $subject, $bodyHtml, $bodyText);

                return $response;
            }
        ], $serviceId
        );
        $this->serviceKernel->first($serviceId);
    }

    // sends an email from an already registered user to a potential new user with an invite code to sign up
    public function sendInviteCode($emails, $inviteCode, $name, $serviceId)
    {
        $this->serviceKernel->register([
            function() use($emails, $inviteCode, $name) {
                Twig_Autoloader::register();
                $loader = new Twig_Loader_Filesystem(__DIR__ . '/MailTemplates');
                $twig = new Twig_Environment($loader);

                $source = 'devdnr@eardish.com';
                $subject = 'Join the Evolution!';
                $bodyText = $twig->render('inviteCode.txt', array(
                    'inviteCode' => $inviteCode,
                    'name' => $name
                ));
                $bodyHtml = $twig->render('inviteCode.mail.twig', array(
                    'inviteCode' => $inviteCode,
                    'name' => $name
                ));

                $response = $this->aws->sendEmail($emails, $source, $subject, $bodyHtml, $bodyText);

                return $response;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    // sends an email from an already registered user to a potential new user with an invite code to sign up
    public function sendBadgeWinnerList($emails, $winners, $weekStart, $weekEnd, $serviceId)
    {
        unset($winners['success']);
        $this->serviceKernel->register([
            function() use($emails, $winners, $weekStart, $weekEnd, $serviceId) {
                Twig_Autoloader::register();
                $loader = new Twig_Loader_Filesystem(__DIR__ . '/MailTemplates');
                $twig = new Twig_Environment($loader);
                $source = 'devdnr@eardish.com';
                $subject = 'Badge Winners!';
                $bodyText = $twig->render('badgeWinners.txt', array(
                    'winners' => $winners,
                    'weekStart' => $weekStart,
                    'weekEnd' => $weekEnd
                ));
                $bodyHtml = $twig->render('badgeWInners.mail.twig', array(
                    'winners' => $winners,
                    'weekStart' => $weekStart,
                    'weekEnd' => $weekEnd
                ));

                $response = $this->aws->sendEmail($emails, $source, $subject, $bodyHtml, $bodyText);

                return $response;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    /**
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}