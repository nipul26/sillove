<?php

namespace Sillove\ProductInquiry\Model;

use Magento\Framework\Session\SessionManagerInterface;

class CaptchaSessionManager
{
    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * CaptchaSessionManager constructor.
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        SessionManagerInterface $sessionManager
    ) {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Clear Captcha Session Data
     *
     * @return void
     */
    public function clearCaptchaSession()
    {
        // Clear the CAPTCHA session data
        $this->sessionManager->start();
        $this->sessionManager->unsCaptchaData();
        $this->sessionManager->writeClose();
    }
}
