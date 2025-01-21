<?php

namespace App\Domain\Key;

use JsonSerializable;

class Key implements JsonSerializable
{
    private ?int $id;
    private string $lastName;
    private string $firstName;
    private string $campusUid;
    private int $empStatus;
    private int $keyNumber;
    private int $keyCore;
    private int $hookNumber;
    private string $roomNumber;
    private string $wingBldg;
    private string $dateCheckedIn;
    private string $dateCheckedOut;
    private string $addNotes;


    /**
     * @param int|null $id
     * @param string $lastName
     * @param string $firstName
     * @param string $campusUid
     * @param int $empStatus
     * @param int $keyNumber
     * @param int $keyCore
     * @param int $hookNumber
     * @param string $roomNumber
     * @param string $wingBldg
     * @param string $dateCheckedIn
     * @param string $dateCheckedOut
     * @param string $addNotes
     */
    public function __construct(
        ?int $id,
        string $lastName,
        string $firstName,
        string $campusUid,
        int $empStatus,
        int $keyNumber,
        int $keyCore,
        int $hookNumber,
        string $roomNumber,
        string $wingBldg,
        string $dateCheckedIn,
        string $dateCheckedOut,
        string $addNotes
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->campusUid = $campusUid;
        $this->empStatus = $empStatus;
        $this->keyNumber = $keyNumber;
        $this->keyCore = $keyCore;
        $this->hookNumber = $hookNumber;
        $this->roomNumber = $roomNumber;
        $this->wingBldg = $wingBldg;
        $this->dateCheckedIn = $dateCheckedIn;
        $this->dateCheckedOut = $dateCheckedOut;
        $this->addNotes = $addNotes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getCampusUid(): string
    {
        return $this->campusUid;
    }

    /**
     * @return string
     */
    public function getAddNotes(): string
    {
        return $this->addNotes;
    }

    /**
     * @return string
     */
    public function getDateCheckedIn(): string
    {
        return $this->dateCheckedIn;
    }

    /**
     * @return string
     */
    public function getDateCheckedOut(): string
    {
        return $this->dateCheckedOut;
    }

    /**
     * @return int
     */
    public function getEmpStatus(): int
    {
        return $this->empStatus;
    }

    /**
     * @return int
     */
    public function getHookNumber(): int
    {
        return $this->hookNumber;
    }

    public function getKeyCore(): int
    {
        return $this->keyCore;
    }

    public function getKeyNumber(): int
    {
        return $this->keyNumber;
    }

    public function getRoomNumber(): string
    {
        return $this->roomNumber;
    }

    public function getWingBldg(): string
    {
        return $this->wingBldg;
    }
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'campusUid' => $this->campusUid,
            'empStatus' => $this->empStatus,
            'keyNumber' => $this->keyNumber,
            'keyCore' => $this->keyCore,
            'hookNumber' => $this->hookNumber,
            'roomNumber' => $this->roomNumber,
            'wingBldg' => $this->wingBldg,
            'dateCheckedIn' => $this->dateCheckedIn,
            'dateCheckedOut' => $this->dateCheckedOut,
            'addNotes' => $this->addNotes
        ];
    }
}
