<?php

require_once '../app/core/Database.php';

class Patient
{
    public static function all()
    {
        return Database::connect()
            ->query("SELECT * FROM patients")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $stmt = Database::connect()->prepare(
            "INSERT INTO patients (name,age,gender,phone,address)
             VALUES (?,?,?,?,?)"
        );
        $stmt->execute([
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address']
        ]);
    }

    public static function update($id,$data)
    {
        $stmt = Database::connect()->prepare(
            "UPDATE patients SET name=?,age=?,gender=?,phone=?,address=? WHERE id=?"
        );
        $stmt->execute([
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address'],
            $id
        ]);
    }

    public static function delete($id)
    {
        Database::connect()
            ->prepare("DELETE FROM patients WHERE id=?")
            ->execute([$id]);
    }
}
