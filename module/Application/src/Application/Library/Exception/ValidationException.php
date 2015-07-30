<?php
namespace Application\Library\Exception;

/**
 * ValidationException class
 */
class ValidationException extends \Exception
{

    /** @var array */
    protected $messages;

    public function __construct($messages, $statusCode)
    {
        //Message is a string
        if (!is_array($messages)) {
            $messages = ['default' => $messages];
        }

        $this->message = 'Validation exception. Use getMessages method to get details.';
        $this->messages = $messages;
        $this->code = $statusCode;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

}