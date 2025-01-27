<?php

namespace App\Domain\Request;

use JsonSerializable;

class Request implements JsonSerializable
{
    private ?int $id;
    private string $last_name;
    private string $first_name;
    private string $email;
    private int $uid;
    private null|string $telephone;
    private null|string $extension;
    private string $pi_supervisor;
    private string $pi_email;
    private int $employment_status;
    private string $request_reason;
    private string $room_one;
    private string $room_two;
    private string $room_three;
    private string $room_four;
    private string $room_five;
    private string $card_access;
    private int $signed;
    private string $justification;
    private string $submitted_by;


    public function __construct(
        ?int $id,
        string $first_name,
        string $last_name,
        string $email,
        int $uid,
        null|string $telephone,
        null|string $extension,
        string $pi_supervisor,
        string $pi_email,
        int $employment_status,
        string $request_reason,
        string $room_one,
        string $room_two,
        string $room_three,
        string $room_four,
        string $room_five,
        string $card_access,
        int $signed,
        string $justification,
        string $submitted_by,
    ) {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->uid = $uid;
        $this->telephone = $telephone;
        $this->extension = $extension;
        $this->pi_supervisor = $pi_supervisor;
        $this->pi_email = $pi_email;
        $this->employment_status = $employment_status;
        $this->request_reason = $request_reason;
        $this->room_one = $room_one;
        $this->room_two = $room_two;
        $this->room_three = $room_three;
        $this->room_four = $room_four;
        $this->room_five = $room_five;
        $this->card_access = $card_access;
        $this->signed = $signed;
        $this->justification = $justification;
        $this->submitted_by = $submitted_by;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getUID(): int
    {
        return $this->uid;
    }

    /**
     * @return null|string
     */
    public function getTelephone(): null|string
    {
        return $this->telephone;
    }

    /**
     * @return null|string
     */
    public function getExtension(): null|string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getPiSupervisor(): string
    {
        return $this->pi_supervisor;
    }

    /**
     * @return string
     */
    public function getPiEmail(): string
    {
        return $this->pi_email;
    }

    /**
     * @return int
     */
    public function getEmpStatus(): int
    {
        return $this->employment_status;
    }

    /**
     * @return string
     */
    public function getRequestReason(): string
    {
        return $this->request_reason;
    }

    public function getRoomOne(): string
    {
        return $this->room_one;
    }

    public function getRoomTwo(): string
    {
        return $this->room_two;
    }

    public function getRoomThree(): string
    {
        return $this->room_three;
    }

    public function getRoomFour(): string
    {
        return $this->room_four;
    }
    public function getRoomFive(): string
    {
        return $this->room_five;
    }

    public function getCardAccess(): string
    {
        return $this->card_access;
    }

    public function getSignature(): int
    {
        return $this->signed;
    }

    public function getJustification(): string
    {
        return $this->justification;
    }

    public function getSubmittedBy(): string
    {
        return $this->submitted_by;
    }
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'email' => $this->email,
            'uid' => $this->uid,
            'telephone' => $this->telephone,
            'extension' => $this->extension,
            'pi_supervisor' => $this->pi_supervisor,
            'pi_email' => $this->pi_email,
            'employment_status' => $this->employment_status,
            'request_reason' => $this->request_reason,
            'room_one' => $this->room_one,
            'room_two' => $this->room_two,
            'room_three' => $this->room_three,
            'room_four' => $this->room_four,
            'room_five' => $this->room_five,
            'card_access' => $this->card_access,
            'signed' => $this->signed,
            'justification' => $this->justification,
            'submitted_by' => $this->submitted_by,

        ];
    }
}
