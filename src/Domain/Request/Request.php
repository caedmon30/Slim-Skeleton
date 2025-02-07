<?php

namespace App\Domain\Request;

use JsonSerializable;
use Nette\Utils\DateTime;

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
    private array $card_access;
    private int $signed;
    private null|string $justification;
    private string $status;
    private string $submitted_by;
    private DateTime $date_submitted;


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
        array $card_access,
        int $signed,
        null|string $justification,
        string $status,
        string $submitted_by,
        DateTime $date_submitted
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
        $this->status = $status;
        $this->submitted_by = $submitted_by;
        $this->date_submitted = $date_submitted;
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

    public function getCardAccess(): array
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

    public function getDateSubmitted(): DateTime
    {
        return $this->date_submitted;
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
            'status' => $this->status,
            'submitted_by' => $this->submitted_by,
            'date_submitted' => $this->date_submitted,

        ];
    }
}
