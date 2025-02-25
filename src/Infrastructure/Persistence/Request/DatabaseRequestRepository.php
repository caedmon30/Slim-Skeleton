<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Request;

use App\Domain\Request\Request;
use App\Domain\Request\RequestNotFoundException;
use App\Domain\Request\RequestRepository;
use App\Services\WorkflowService;
use Nette\Database\Connection;

class DatabaseRequestRepository implements RequestRepository
{
    private array $requests;
    private Connection $connection;

    public function __construct(Connection $connection, array $requests = [])
    {

        $this->connection = $connection;

        $isAdmin = $_SESSION['role'] === 'admin'; // Check if the user is an admin
        $loggedInUserId = $_SESSION['username']; // Get the currently logged-in user ID
        $piEmail = $_SESSION['email']; // Get the PI email address

        if (!$isAdmin) {
            // If the user is NOT an admin
            if ($loggedInUserId) {
                // Filter by 'submitted_by' or 'pi_email' (the user submitted the request or is the PI)
                $results = $this->connection->query('SELECT * FROM requests WHERE', [
                    $this->connection::literal('?or', [
                        'submitted_by' => $loggedInUserId,
                        'pi_email' => $piEmail,
                    ]),
                ]);
            } else {
                // If the user is not the one who submitted the request and is not the PI
                $results = [];
            }
        } else {
            // Admin can see all records
            $results = $this->connection->query("SELECT * FROM requests");
        }


        foreach ($results as $row) {
            $requests[(int)$row['id']] = new Request(
                (int)$row->id,
                $row->first_name,
                $row->last_name,
                $row->email,
                $row->uid,
                $row->telephone,
                $row->extension,
                $row->pi_supervisor,
                $row->pi_email,
                $row->employment_status,
                $row->request_reason,
                $row->room_one,
                $row->room_two,
                $row->room_three,
                $row->room_four,
                $row->room_five,
                $row->card_access = unserialize($row->card_access),
                $row->signed,
                $row->justification,
                $row->status,
                $row->submitted_by,
                $row->date_submitted,


            );
        }
        $this->requests = $requests;
    }


    public function findAll(): array
    {

        return array_values($this->requests);
    }

    /**
     * @throws RequestNotFoundException
     */
    public function findRequestOfId(int $id): array
    {
        if (!empty($id)) {
            throw new RequestNotFoundException();
        }
        $data = (array)$this->connection->query('SELECT * FROM requests WHERE id = ?', $id)->fetch();
        if (!(empty($data['card_access']))) {
            $data['card_access'] = unserialize($data['card_access']);
        }
        $data['approver_id'] = strtok($data['pi_email'], '@');
        $list = [$data['room_one'], $data['room_two'], $data['room_three'], $data['room_four'], $data['room_five']];
        $data['rooms'] = array_filter($list);
        return $data;
    }

    /**
     * @throws RequestNotFoundException
     */
    public function deleteRequestOfId(int $id): array
    {
        if (!isset($this->requests[$id])) {
            throw new RequestNotFoundException();
        }

        $this->connection->query('DELETE FROM requests WHERE id = ?', $id);
        return array_values((array)$this->connection->query("SELECT * FROM requests")->fetchAll());
    }


    /**
     * @throws RequestNotFoundException
     */
    public function updateRequestOfId(int $id, array $data): int
    {

        if (!isset($this->requests[$id])) {
            throw new RequestNotFoundException();
        }
        $this->connection->query('UPDATE requests SET ? WHERE id = ?', $data, $id);
        return (int)$this->connection->getInsertId();
    }

    public function createRequest(array $data): int
    {
        $this->connection->query('INSERT INTO requests ?', $data);
        return (int)$this->connection->getInsertId();
    }
}
