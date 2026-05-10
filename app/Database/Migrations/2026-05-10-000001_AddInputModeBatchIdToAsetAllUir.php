<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInputModeBatchIdToAsetAllUir extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE aset_all_uir ADD COLUMN input_mode ENUM('single', 'bulk') NOT NULL DEFAULT 'single' AFTER deleted_at");
        $this->db->query("ALTER TABLE aset_all_uir ADD COLUMN batch_id VARCHAR(50) NULL AFTER input_mode");
        $this->db->query("ALTER TABLE aset_all_uir ADD INDEX idx_input_mode (input_mode)");
        $this->db->query("ALTER TABLE aset_all_uir ADD INDEX idx_batch_id (batch_id)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE aset_all_uir DROP INDEX idx_batch_id");
        $this->db->query("ALTER TABLE aset_all_uir DROP INDEX idx_input_mode");
        $this->db->query("ALTER TABLE aset_all_uir DROP COLUMN batch_id");
        $this->db->query("ALTER TABLE aset_all_uir DROP COLUMN input_mode");
    }
}