<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetAllUirTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'all_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
            ],
            'nomor_aset_baru' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'unique'     => true,
            ],
            'nomor_aset_lama' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'index'      => true,
            ],
            'nama_aset' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'index'      => true,
            ],
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'sub_kategori_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'golongan_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
                'index'      => true,
            ],
            'merk_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
                'index'      => true,
            ],
            'type_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
                'index'      => true,
            ],
            'kondisi_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'sumber_dana_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
                'index'      => true,
            ],
            'unit_kerja_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
                'comment'    => 'Relasi logis ke tbl_unit_kerja.id_unit_kerja (bukan FK fisik)',
            ],
            'sub_unit_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
                'index'      => true,
            ],
            'ruangan_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
                'index'      => true,
            ],
            'penanggung_jawab_id_kpe' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'index'      => true,
                'comment'    => 'Relasi logis ke ylpi_karyawan.id_kpe (bukan FK fisik)',
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'index'      => true,
            ],
            'spesifikasi' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'tahun_perolehan' => [
                'type'       => 'YEAR',
                'null'       => true,
                'index'      => true,
            ],
            'tanggal_perolehan' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'nilai_perolehan' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
            ],
            'status_aset' => [
                'type'       => 'ENUM',
                'constraint' => ['draft','aktif','nonaktif','hilang','dihapus'],
                'default'    => 'draft',
                'index'      => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'index'      => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'index'      => true,
            ],
        ]);

        $this->forge->addKey('all_id', true);
        
        // Create the table first
        $this->forge->createTable('aset_all_uir');
        
        // Add foreign keys for internal references only
        // Note: No FK constraints for external references (unit_kerja_id, penanggung_jawab_id_kpe)
        // as they refer to tables in different databases
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_kategori_id FOREIGN KEY (kategori_id) REFERENCES aset_kategori(kt_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_sub_kategori_id FOREIGN KEY (sub_kategori_id) REFERENCES aset_sub_kategori(sk_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_golongan_id FOREIGN KEY (golongan_id) REFERENCES aset_golongan(gl_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_merk_id FOREIGN KEY (merk_id) REFERENCES aset_merk(mr_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_type_id FOREIGN KEY (type_id) REFERENCES aset_type(ty_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_kondisi_id FOREIGN KEY (kondisi_id) REFERENCES aset_kondisi_barang(kd_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_sumber_dana_id FOREIGN KEY (sumber_dana_id) REFERENCES aset_sumber_dana(sd_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_sub_unit_id FOREIGN KEY (sub_unit_id) REFERENCES aset_sub_units(su_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
        $this->db->query("ALTER TABLE aset_all_uir ADD CONSTRAINT fk_aset_ruangan_id FOREIGN KEY (ruangan_id) REFERENCES aset_ruangan(rg_id) ON UPDATE CASCADE ON DELETE RESTRICT;");
    }

    public function down()
    {
        $this->forge->dropTable('aset_all_uir');
    }
}