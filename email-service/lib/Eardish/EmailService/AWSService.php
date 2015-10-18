<?php

namespace Eardish\EmailService;

use Aws\Ses\SesClient;
use Aws\Common\Exception\InstanceProfileCredentialsException as IPCredentialsException;
use Guzzle\Service\Resource\Model;

class AWSService {

//    protected $emailService;
    protected $emailArgs = array();
    /**
     * @var SesClient
     */
    protected $ses;

    /**
     * AWS Region
     *
     * @var string
     */
    protected $region;

    /**
     * @param \Eardish\AppConfig $config
     *
     * @throws IPCredentialsException
     */
    function __construct($config)
    {
        $this->region = $config->get('email.aws.region');
        $awsConfigRaw = file_get_contents($config->get('aws-path'));
        $awsConfig = json_decode($awsConfigRaw, true);

        if ($awsConfig['email']['key'] && $awsConfig['email']['id']) {
            $this->ses = SesClient::factory(array(
                'credentials' => array(
                    'profile' => 'default',
                    'key'      => $awsConfig['email']['id'],
                    'secret'   => $awsConfig['email']['key'],
                ),
                'region' => $this->region,
                // AWS PHP SDK version 3 requires an AWS API version...in order to preserve backwards compatibility we might want to hard code an actual version here
                'version' => 'latest',
                'debug' => false,
                'retries' => 5
            ));
        } else {
            throw new IPCredentialsException("AWS Key(s) environment variables are not set. Terminating.");
        }
//        $this->emailService = $emailService;
    }

    public function sendEmail($emails = array(), $source, $subject, $bodyHtml, $bodyText)
    {
        $this->emailArgs = array(
            // Source is required
            //TODO Add actual source email
            'Source' => $source,
            // Destination is required
            'Destination' => array(
                'ToAddresses' => $emails
            ),
            // Message is required
            'Message' => array(
                // Subject is required
                'Subject' => array(
                    // Data is required
                    'Data' => $subject,
                    'Charset' => 'UTF-8',
                ),
                // Body is required
                'Body' => array(
                    'Text' => array(
                        // Data is required
                        // Text version
                        'Data' => $bodyText,
                        'Charset' => 'UTF-8',
                    ),
                    'Html' => array(
                        // Data is required
                        // Html version
                        'Data' => $bodyHtml,
                        'Charset' => 'UTF-8',
                    ),
                ),
            ),
            //TODO Add replyTo and returnPath addresses if needed
            'ReplyToAddresses' => array( 'devdnr@eardish.com' ),
            'ReturnPath' => 'devdnr@eardish.com'
        );

        /**
         * @var \Guzzle\Service\Resource\Model
         */
        $response = $this->ses->sendEmail($this->emailArgs);

        if ($response instanceof Model) {
            return ['data' => ['email' => true]];
        }

        return ['data' => ['email' => false]];
    }
//
//    public function constructSesEmail($toAddresses, $source, $subject, $body)
//    {
//        $this->emailArgs = array(
//            // Source is required
//            //TODO Add actual source email
//            'Source' => $source,
//            // Destination is required
//            'Destination' => array(
//                'ToAddresses' => array($toAddresses)
//            ),
//            // Message is required
//            'Message' => array(
//                // Subject is required
//                'Subject' => array(
//                    // Data is required
//                    'Data' => $subject,
//                    'Charset' => 'UTF-8',
//                ),
//                // Body is required
//                'Body' => array(
//                    'Text' => array(
//                        // Data is required
//                        'Data' => $body,
//                        'Charset' => 'UTF-8',
//                    ),
//                    'Html' => array(
//                        // Data is required
//                        //TODO fill in html data
//                        'Data' => '<b>My HTML Email</b>',
//                        'Charset' => 'UTF-8',
//                    ),
//                ),
//            ),
//            //TODO Add replyTo and returnPath addresses if needed
//            'ReplyToAddresses' => array( 'devdnr@eardish.com' ),
//            'ReturnPath' => 'devdnr@eardish.com'
//        );
//
//        return $this->ses->constructSesEmail($this->emailArgs);
//    }
}