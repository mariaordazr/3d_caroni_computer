<?php
class Client {
    private $db;

    public function __construct($connection) {
        $this->db = $connection;
        $this->ensureColumnsExist();
    }

    private function ensureColumnsExist() {
        $checkRating = @$this->db->query("SHOW COLUMNS FROM clients LIKE 'rating'");
        if ($checkRating && $checkRating->num_rows === 0) {
            @$this->db->query("ALTER TABLE clients ADD COLUMN rating TINYINT NOT NULL DEFAULT 0");
        } else {
            @$this->db->query("ALTER TABLE clients ALTER COLUMN rating SET DEFAULT 0");
        }

        $checkNotes = @$this->db->query("SHOW COLUMNS FROM clients LIKE 'notes'");
        if ($checkNotes && $checkNotes->num_rows === 0) {
            @$this->db->query("ALTER TABLE clients ADD COLUMN notes VARCHAR(255) NOT NULL DEFAULT ''");
        }

        $checkPhone = @$this->db->query("SHOW COLUMNS FROM clients LIKE 'client_phone'");
        if ($checkPhone && $checkPhone->num_rows === 0) {
            @$this->db->query("ALTER TABLE clients ADD COLUMN client_phone VARCHAR(50) NOT NULL DEFAULT ''");
        }

        $checkEmail = @$this->db->query("SHOW COLUMNS FROM clients LIKE 'client_email'");
        if ($checkEmail && $checkEmail->num_rows === 0) {
            @$this->db->query("ALTER TABLE clients ADD COLUMN client_email VARCHAR(150) NULL DEFAULT NULL");
        }

        $checkAddress = @$this->db->query("SHOW COLUMNS FROM clients LIKE 'client_address'");
        if ($checkAddress && $checkAddress->num_rows === 0) {
            @$this->db->query("ALTER TABLE clients ADD COLUMN client_address VARCHAR(255) NOT NULL DEFAULT 'Puerto Ordaz'");
        }
    }

    public function getAll(array $filters = []) {
        $query = "SELECT * FROM clients";
        if (!empty($filters['search'])) {
            $search = '%' . $this->db->real_escape_string($filters['search']) . '%';
            $query .= " WHERE client_id_number LIKE '$search' OR client_name LIKE '$search' OR client_phone LIKE '$search' OR client_email LIKE '$search' OR notes LIKE '$search'";
        }
        $query .= " ORDER BY id_client DESC";
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id_client = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByDni(string $dni) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE client_id_number = ? LIMIT 1");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create(array $data) {
        $rating  = isset($data['rating']) ? max(0, min(5, (int)$data['rating'])) : 0;
        $notes   = trim($data['notes'] ?? '');
        $phone   = trim($data['client_phone'] ?? '');
        $email   = !empty(trim($data['client_email'] ?? '')) ? trim($data['client_email']) : null;
        $address = !empty(trim($data['client_address'] ?? '')) ? trim($data['client_address']) : 'Puerto Ordaz';

        $sql = "INSERT INTO clients (client_id_number, client_name, client_phone, client_email, client_address, rating, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssis", $data['client_id_number'], $data['client_name'], $phone, $email, $address, $rating, $notes);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function update(int $id, array $data) {
        $rating  = isset($data['rating']) ? max(0, min(5, (int)$data['rating'])) : 0;
        $notes   = trim($data['notes'] ?? '');
        $phone   = trim($data['client_phone'] ?? '');
        $email   = !empty(trim($data['client_email'] ?? '')) ? trim($data['client_email']) : null;
        $address = !empty(trim($data['client_address'] ?? '')) ? trim($data['client_address']) : 'Puerto Ordaz';

        $sql = "UPDATE clients SET client_id_number = ?, client_name = ?, client_phone = ?, client_email = ?, client_address = ?, rating = ?, notes = ? WHERE id_client = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssisi", $data['client_id_number'], $data['client_name'], $phone, $email, $address, $rating, $notes, $id);
        return $stmt->execute();
    }

    public function upsertFromModal(string $dni, string $name, int $rating, string $notes, int $idClient = 0, string $phone = '', ?string $email = null, string $address = 'Puerto Ordaz') {
        $rating  = max(0, min(5, $rating));
        $email   = !empty(trim($email ?? '')) ? trim($email) : null;
        $address = !empty(trim($address)) ? trim($address) : 'Puerto Ordaz';

        $payload = [
            'client_id_number' => $dni,
            'client_name'      => $name,
            'client_phone'     => trim($phone),
            'client_email'     => $email,
            'client_address'   => $address,
            'rating'           => $rating,
            'notes'            => $notes
        ];

        if ($idClient > 0) {
            return $this->update($idClient, $payload) ? $idClient : false;
        }
        $existing = $this->getByDni($dni);
        if ($existing) {
            $this->update((int)$existing['id_client'], $payload);
            return (int)$existing['id_client'];
        }
        return $this->create($payload);
    }

    public function syncContactFromReceipt($dni, $name, $phone, $email, $address) {
        $dni = trim($dni);
        if (empty($dni) || empty($name)) {
            return false;
        }

        $phone = trim($phone ?? '');
        $email = !empty(trim($email ?? '')) ? trim($email) : null;
        $address = !empty(trim($address ?? '')) ? trim($address) : 'Puerto Ordaz';

        // Verificar si el cliente ya existe por su DNI/RIF
        $existing = $this->getByDni($dni);

        if ($existing) {
            $idClient = (int)$existing['id_client'];

            // Si en el formulario se colocó un dato (nuevo o modificado), se actualiza.
            // Si en el formulario se dejó vacío el teléfono, conservamos el que tenía por seguridad.
            $newPhone = ($phone !== '') ? $phone : ($existing['client_phone'] ?? '');
            
            // El correo y la dirección toman el valor del formulario (permitiendo agregarlos o cambiarlos)
            $newEmail = $email; 
            $newAddress = ($address !== '') ? $address : (!empty($existing['client_address']) ? $existing['client_address'] : 'Puerto Ordaz');

            $stmt = $this->db->prepare("
                UPDATE clients 
                SET client_name = ?, 
                    client_phone = ?, 
                    client_email = ?, 
                    client_address = ? 
                WHERE id_client = ?
            ");
            $stmt->bind_param("ssssi", $name, $newPhone, $newEmail, $newAddress, $idClient);
            return $stmt->execute();
        } else {
            // Si el cliente no existe, lo creamos asociándole todos los datos de la nota
            $defaultRating = 5;
            $defaultNotes = '';
            $stmt = $this->db->prepare("
                INSERT INTO clients (client_id_number, client_name, client_phone, client_email, client_address, rating, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssssis", $dni, $name, $phone, $email, $address, $defaultRating, $defaultNotes);
            return $stmt->execute();
        }
    }

    public function delete(int $id) {
        $stmt = $this->db->prepare("DELETE FROM clients WHERE id_client = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>