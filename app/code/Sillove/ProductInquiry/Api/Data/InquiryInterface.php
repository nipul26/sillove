<?php

namespace Sillove\ProductInquiry\Api\Data;

interface InquiryInterface
{
    public const ID = 'id';
    public const PRD_ENTITY_ID = 'prd_entity_id';
    public const PRD_SKU = 'prd_sku';
    public const USR_NAME = 'usr_name';
    public const EMAIL = 'email';
    public const SUBJECT = 'subject';
    public const INQ_MSG = 'inq_msg';
    public const PUBLISH_DATETIME = 'publish_datetime';
    public const UPDATE_DATETIME = 'update_datetime';

    /**
     * Get Id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Set Id
     *
     * @param int $Id
     * @return mixed
     */
    public function setId($Id);

    /**
     * Get Product Entity Id
     *
     * @return mixed
     */
    public function getPrdEntityId();

    /**
     * Set Product Entity Id
     *
     * @param int $prd_entity_id
     * @return mixed
     */
    public function setPrdEntityId($prd_entity_id);

    /**
     * Get Product Sku
     *
     * @return mixed
     */
    public function getPrdSku();

    /**
     * Set Product Sku
     *
     * @param string $prd_sku
     * @return mixed
     */
    public function setPrdSku($prd_sku);

    /**
     * Get Username
     *
     * @return mixed
     */
    public function getUsrName();

    /**
     * Set Username
     *
     * @param string $usr_name
     * @return mixed
     */
    public function setUsrName($usr_name);

    /**
     * Get Email
     *
     * @return mixed
     */
    public function getEmail();

    /**
     * Set Email
     *
     * @param string $email
     * @return mixed
     */
    public function setEmail($email);

    /**
     * Get Subject
     *
     * @return mixed
     */
    public function getSubject();

    /**
     * Set Subject
     *
     * @param array $subject
     * @return mixed
     */
    public function setSubject($subject);

    /**
     * Get Inquiry Message
     *
     * @return mixed
     */
    public function getInqMsg();

    /**
     * Set Inquiry Message
     *
     * @param string $inq_msg
     * @return mixed
     */
    public function setInqMsg($inq_msg);

    /**
     * Get Publish DateTime
     *
     * @return mixed
     */
    public function getPublishDatetime();

    /**
     * Set Publish DateTime
     *
     * @param string $publish_datetime
     * @return mixed
     */
    public function setPublishDatetime($publish_datetime);

    /**
     * Get Update DateTime
     *
     * @return mixed
     */
    public function getUpadteDatetime();

    /**
     * Set Update DateTime
     *
     * @param string $update_datetime
     * @return mixed
     */
    public function setUpadteDatetime($update_datetime);
}
