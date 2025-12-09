<?php

namespace HoomanMirghasemi\Sms\Drivers;

use HoomanMirghasemi\Sms\Abstracts\Driver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use SoapClient;

class Magfa extends Driver
{
    private \SoapClient $soapClient;

    public function __construct(protected array $settings)
    {
        $this->from = data_get($this->settings, 'from');
        $this->tryConnectToProvider();
    }

    /**
     * Send sms method for Magfa.
     *
     * This method send sms and save log to db.
     */
    public function send(): bool
    {
        if (! $this->serviceActive) {
            parent::failedConnectToProvider();

            return false;
        }
        $response = $this->soapClient->send([$this->getMessage()], [$this->from], [$this->recipient]);
        if (0 == $response->status) {
            $this->webserviceResponse = "Message has been successfully sent ; MessageId : {$response->messages->id}";
            $this->success = true;
        }

        $this->webserviceResponse = "An error occurred \n";
        $this->webserviceResponse .= "Error Code : $response->status ; Error Title : ".$this->getErrors()[$response->status]['title'].' {'.$this->getErrors()[$response->status]['desc'].'}';
        $this->success = false;

        return parent::send();
    }

    /**
     * Return the remaining balance of magfa.
     */
    public function getBalance(): string
    {
        if (! $this->serviceActive) {
            return 'وب سرویس مگفا با مشکل مواجه شده.';
        }

        try {
            $response = $this->soapClient->balance();

            return $response->balance;
        } catch (\Exception $e) {
            return 'message:'.$e->getMessage().' code: '.$e->getCode();
        }
    }

    /**
     * Make SoapClient object and connect to magfa wsdl webservices.
     */
    private function tryConnectToProvider(): void
    {
        try {
            $this->soapClient = new \SoapClient(data_get($this->settings, 'wsdl_url'), [
                'login'       => data_get($this->settings, 'username').'/'.data_get($this->settings, 'domain'),
                'password'    => data_get($this->settings, 'password'),
                'cache_wsdl'  => WSDL_CACHE_NONE, // -No WSDL Cache
                'compression' => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5), // -Compression *
                'trace'       => App::environment(['local', 'staging', 'testing']), // -Optional (debug)
            ]);
        } catch (\SoapFault $soapFault) {
            Log::error('magfa sms code: '.$soapFault->getCode().' message: '.$soapFault->getMessage());
            $this->serviceActive = false;
        }
    }

    /**
     * Return error messages for SmsMagfa.
     */
    private function getErrors(): array
    {
        $errors = [];
        $errors[1]['title'] = 'INVALID_RECIPIENT_NUMBER';
        $errors[1]['desc'] = 'the string you presented as recipient numbers are not valid phone numbers, please check them again';

        $errors[2]['title'] = 'INVALID_SENDER_NUMBER';
        $errors[2]['desc'] = 'the string you presented as sender numbers(3000-xxx) are not valid numbers, please check them again';

        $errors[3]['title'] = 'INVALID_ENCODING';
        $errors[3]['desc'] = 'are You sure You\'ve entered the right encoding for this message? You can try other encodings to bypass this error code';

        $errors[4]['title'] = 'INVALID_MESSAGE_CLASS';
        $errors[4]['desc'] = 'entered MessageClass is not valid. for a normal MClass, leave this entry empty';

        $errors[6]['title'] = 'INVALID_UDH';
        $errors[6]['desc'] = 'entered UDH is invalid. in order to send a simple message, leave this entry empty';

        $errors[12]['title'] = 'INVALID_ACCOUNT_ID';
        $errors[12]['desc'] = 'you\'re trying to use a service from another account??? check your UN/Password/NumberRange again';

        $errors[13]['title'] = 'NULL_MESSAGE';
        $errors[13]['desc'] = 'check the text of your message. it seems to be null';

        $errors[14]['title'] = 'CREDIT_NOT_ENOUGH';
        $errors[14]['desc'] = 'Your credit\'s not enough to send this message. you might want to buy some credit.call';

        $errors[15]['title'] = 'SERVER_ERROR';
        $errors[15]['desc'] = 'something bad happened on server side, you might want to call MAGFA Support about this:';

        $errors[16]['title'] = 'ACCOUNT_INACTIVE';
        $errors[16]['desc'] = 'Your account is not active right now, call -- to activate it';

        $errors[17]['title'] = 'ACCOUNT_EXPIRED';
        $errors[17]['desc'] = 'looks like Your account\'s reached its expiration time, call -- for more information';

        $errors[18]['title'] = 'INVALID_USERNAME_PASSWORD_DOMAIN'; // todo : note : one of them are empty
        $errors[18]['desc'] = 'the combination of entered Username/Password/Domain is not valid. check\'em again';

        $errors[19]['title'] = 'AUTHENTICATION_FAILED'; // todo : note : wrong arguments supplied ...
        $errors[19]['desc'] = 'You\'re not entering the correct combination of Username/Password';

        $errors[20]['title'] = 'SERVICE_TYPE_NOT_FOUND';
        $errors[20]['desc'] = 'check the service type you\'re requesting. we don\'t get what service you want to use. your sender number might be wrong, too.';

        $errors[22]['title'] = 'ACCOUNT_SERVICE_NOT_FOUND';
        $errors[22]['desc'] = 'your current number range does\'t have the permission to use Webservices';

        $errors[23]['title'] = 'SERVER_BUSY';
        $errors[23]['desc'] = 'Sorry, Server\'s under heavy traffic pressure, try testing another time please';

        $errors[24]['title'] = 'INVALID_MESSAGE_ID';
        $errors[24]['desc'] = 'entered message-id seems to be invalid, are you sure You entered the right thing?';

        $errors[25]['title'] = 'INVALID_METHOD_NAME';
        $errors[25]['desc'] = 'the method name call is invalid';

        $errors[27]['title'] = 'RECEIVER_NUMBER_IN_BLACK_LIST';
        $errors[27]['desc'] = 'the receiver number is in the black list';

        $errors[28]['title'] = 'PREFIX_RECEIVER_NUMBER_IS_BLOCKED';
        $errors[28]['desc'] = 'the receiver number is in the block list of magfa';

        $errors[29]['title'] = 'YOU_IP_ADDRESS_NOT_VALID';
        $errors[29]['desc'] = 'server ip address is not valid to use service';

        $errors[30]['title'] = 'LARGE_SMS_CONTENT';
        $errors[30]['desc'] = 'the sms content is more than 255 character';

        $errors[101]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_MESSAGE_CLASS_ARRAY_BODIES';
        $errors[101]['desc'] = 'this happens when you try to define MClasses for your messages. in this case you must define one recipient number for each MClass';

        $errors[102]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_MESSAGE_CLASS_ARRAY';
        $errors[102]['desc'] = 'this happens when you try to define MClasses for your messages. in this case you must define one recipient number for each MClass';

        $errors[103]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_SENDER_NUMBER_ARRAY';
        $errors[103]['desc'] = 'This error happens when you have more than one sender-number for message. when you have more than one sender number, for each sender-number you must define a recipient number...';

        $errors[104]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_MESSAGE_ARRAY';
        $errors[104]['desc'] = 'this happens when you try to define UDHs for your messages. in this case you must define one recipient number for each udh';

        $errors[105]['title'] = 'PRIORITIES_ARRAY_SIZE_NOT_EQUAL_RECEIVERS';
        $errors[105]['desc'] = 'priorities array size not equal receivers';

        $errors[106]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_IS_NULL';
        $errors[106]['desc'] = 'array of recipient numbers must have at least one member';

        $errors[107]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_TOO_LONG';
        $errors[107]['desc'] = 'the maximum number of recipients per message is 90';

        $errors[108]['title'] = 'WEB_SENDER_NUMBER_ARRAY_IS_NULL';
        $errors[108]['desc'] = 'array of sender numbers must have at least one member';

        $errors[109]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_ENCODING_ARRAY';
        $errors[109]['desc'] = 'this happens when you try to define encodings for your messages. in this case you must define one recipient number for each Encoding';

        $errors[110]['title'] = 'WEB_RECIPIENT_NUMBER_ARRAY_SIZE_NOT_EQUAL_CHECKING_MESSAGE_IDS__ARRAY';
        $errors[110]['desc'] = 'this happens when you try to define checking-message-ids for your messages. in this case you must define one recipient number for each checking-message-id';

        $errors[-1]['title'] = 'NOT_AVAILABLE';
        $errors[-1]['desc'] = 'The target of report is not available(e.g. no message is associated with entered IDs)';

        return $errors;
    }
}
