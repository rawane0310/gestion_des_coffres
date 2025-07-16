<?php

namespace App\Service;

use App\Repository\HistoriqueRepository;

class HistoriqueManager
{
    private HistoriqueRepository $historiqueRepository;

    public function __construct(HistoriqueRepository $historiqueRepository)
    {
        $this->historiqueRepository = $historiqueRepository;
    }

    public function getHistoriquesByCoffreId(?int $coffreId): array
    {
        if ($coffreId === null) {
            return [
                'message' => ['error' => 'Coffre ID is required'],
                'status' => 400
            ];
        }

        $historiques = $this->historiqueRepository->findBy(['coffre' => $coffreId]);

        $data = array_map(function ($h) {
            return [
                'id' => $h->getId(),
                'old_code' => $h->getCode(),
                'date' => $h->getCreatedAt()?->format('Y-m-d H:i:s'),
                'changed_by' => $h->getChangedBy()?->getEmail()
            ];
        }, $historiques);

        return [
            'message' => [
                'success' => true,
                'data' => $data
            ],
            'status' => 200
        ];
    }
}
