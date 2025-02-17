<?php

class Player {
    private $id;
    private $name;
    private $team;
    private $position;
    private $age;
    private $nationality;
    private $goalsScored;
    private $imageUrl;
    private $validationErrors = [];

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->team = $data['team'] ?? '';
        $this->position = $data['position'] ?? '';
        $this->age = $data['age'] ?? null;
        $this->nationality = $data['nationality'] ?? '';
        $this->goalsScored = $data['goals_scored'] ?? 0;
        $this->imageUrl = $data['image_url'] ?? '';
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getTeam(): string {
        return $this->team;
    }

    public function getPosition(): string {
        return $this->position;
    }

    public function getAge(): ?int {
        return $this->age;
    }

    public function getNationality(): string {
        return $this->nationality;
    }

    public function getGoalsScored(): int {
        return $this->goalsScored;
    }

    public function getImageUrl(): string {
        return $this->imageUrl;
    }

    public function getValidationError(): string {
        return !empty($this->validationErrors) ? $this->validationErrors[0] : '';
    }

    // Méthode pour convertir l'objet en tableau
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'team' => $this->team,
            'position' => $this->position,
            'age' => $this->age,
            'nationality' => $this->nationality,
            'goals_scored' => $this->goalsScored,
            'image_url' => $this->imageUrl
        ];
    }

    // Validation
    public function isValid(): bool {
        $this->validationErrors = [];

        // Vérifier les champs requis
        $requiredFields = ['name', 'team', 'position', 'nationality'];
        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                $this->validationErrors[] = "Le champ {$field} est requis";
                return false;
            }
        }

        // Valider l'âge
        if (!is_numeric($this->age) || $this->age < 15 || $this->age > 45) {
            $this->validationErrors[] = "Âge invalide";
            return false;
        }

        // Valider la position
        if (!in_array($this->position, self::getValidPositions())) {
            $this->validationErrors[] = "Position invalide";
            return false;
        }

        // Valider le nombre de buts
        if (!is_numeric($this->goalsScored) || $this->goalsScored < 0) {
            $this->validationErrors[] = "Nombre de buts invalide";
            return false;
        }

        return true;
    }

    // Positions valides
    public static function getValidPositions(): array {
        return ['Attaquant', 'Milieu', 'Défenseur', 'Gardien'];
    }
} 