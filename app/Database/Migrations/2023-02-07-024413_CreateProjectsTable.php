<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 5,
                "unsigned" => true,
                "auto_increment" => true
            ],
            "user_id" => [
                "type" => "INT",
                "constraint" => 5,
            ],
            "title" => [
                "type" => "VARCHAR",
                "constraint" => 150,
                "null" => false
            ],
            "budget" => [
                "type" => "INT",
                "constraint" => 5,
            ]
        ]);
        $this->forge->addPrimaryKey("id");
        $this->forge->createTable("projects");
    }

    public function down()
    {
        $this->forge->dropTable("projects");
    }
}
