<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508195143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tuitions (id INT AUTO_INCREMENT NOT NULL, student_key VARCHAR(8) NOT NULL, surnames VARCHAR(36) NOT NULL, name VARCHAR(24) NOT NULL, born VARCHAR(24) NOT NULL, province VARCHAR(16) NOT NULL, birthday DATE NOT NULL, home VARCHAR(64) NOT NULL, location VARCHAR(24) NOT NULL, dni VARCHAR(13) NOT NULL, father VARCHAR(48) NOT NULL, id_p_first_tutor VARCHAR(13) NOT NULL, mother VARCHAR(48) NOT NULL, id_p_second_tutor VARCHAR(13) NOT NULL, first_phone INT NOT NULL, second_phone INT NOT NULL, school VARCHAR(64) NOT NULL, other_school VARCHAR(64) DEFAULT NULL, group_letter VARCHAR(1) DEFAULT NULL, email VARCHAR(36) DEFAULT NULL, language VARCHAR(6) NOT NULL, religion VARCHAR(22) NOT NULL, first_optional INT NOT NULL, second_optional INT NOT NULL, third_optional INT NOT NULL, fourth_optional INT NOT NULL, first_act INT DEFAULT NULL, second_act INT DEFAULT NULL, third_act INT DEFAULT NULL, fourh_act INT DEFAULT NULL, second_first_optional INT DEFAULT NULL, second_second_optional INT DEFAULT NULL, secont_third_optional INT DEFAULT NULL, second_fourth_optional INT DEFAULT NULL, second_first_act INT DEFAULT NULL, second_second_act INT DEFAULT NULL, second_third_act INT DEFAULT NULL, second_fourth_act INT DEFAULT NULL, observations LONGTEXT DEFAULT NULL, exemption INT DEFAULT NULL, bilingualism VARCHAR(2) DEFAULT NULL, course VARCHAR(5) NOT NULL, date DATETIME NOT NULL, promotes INT DEFAULT NULL, transport INT DEFAULT NULL, east_route VARCHAR(42) DEFAULT NULL, west_route VARCHAR(42) DEFAULT NULL, sex VARCHAR(6) NOT NULL, brothers INT DEFAULT NULL, nationality VARCHAR(32) NOT NULL, itinerary INT DEFAULT NULL, fourth_optionals VARCHAR(32) DEFAULT NULL, fifth_optional INT DEFAULT NULL, sixth_optional INT DEFAULT NULL, seventh_optional INT DEFAULT NULL, diversificate INT DEFAULT NULL, second_fifth_optional INT DEFAULT NULL, second_sixth_optional INT DEFAULT NULL, second_seventh_optional INT DEFAULT NULL, confirmed INT DEFAULT NULL, administrator INT DEFAULT NULL, actual_group VARCHAR(2) DEFAULT NULL, revised INT DEFAULT NULL, disease VARCHAR(254) NOT NULL, other_disease VARCHAR(254) NOT NULL, photo INT NOT NULL, divorce VARCHAR(64) DEFAULT NULL, third_math VARCHAR(1) NOT NULL, fourth_science VARCHAR(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tuitions');
    }
}
