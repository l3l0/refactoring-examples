<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220611114016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add medical examination order and medical results tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE medical_examination_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medical_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE medical_examination_order (id INT NOT NULL, ordering_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, patient_identification_number INT NOT NULL, version VARCHAR(64) DEFAULT NULL, token VARCHAR(32) NOT NULL, agreement_number VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN medical_examination_order.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN medical_examination_order.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE medical_result (id INT NOT NULL, token VARCHAR(32) NOT NULL, agreement_number VARCHAR(255) DEFAULT NULL, result_document_id VARCHAR(255) NOT NULL, required_decision_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, treatment_decision VARCHAR(255) DEFAULT NULL, treatment_decision_type VARCHAR(255) DEFAULT NULL, client_ip_address VARCHAR(255) DEFAULT NULL, decision_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN medical_result.required_decision_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN medical_result.decision_date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE medical_examination_order_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medical_result_id_seq CASCADE');
        $this->addSql('DROP TABLE medical_examination_order');
        $this->addSql('DROP TABLE medical_result');
    }
}
