<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521181202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cteachers');
        $this->addSql('DROP TABLE tuitions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cteachers (id_teacher INT NOT NULL, pass VARCHAR(48) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, name_teacher VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, dni VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, idea VARCHAR(12) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, id_department INT NOT NULL, email VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, state TINYINT(1) NOT NULL, phone VARCHAR(9) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, alias_teacher VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, id_language TINYINT(1) NOT NULL, top_secret VARCHAR(16) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, consent TINYINT(1) NOT NULL, email_warning TINYINT(1) DEFAULT NULL, tmp TINYINT(1) DEFAULT NULL, personal_warning TINYINT(1) DEFAULT NULL, shortcut_menu TINYINT(1) NOT NULL, side_shortcut_menu TINYINT(1) NOT NULL, asistence_number INT DEFAULT NULL, highschool_email VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, caped INT NOT NULL, PRIMARY KEY(id_teacher)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tuitions (id INT AUTO_INCREMENT NOT NULL, student_key VARCHAR(8) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, surnames VARCHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, born VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, province VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, birthday DATE NOT NULL, home VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, location VARCHAR(24) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, dni VARCHAR(13) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, father VARCHAR(48) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, id_p_first_tutor VARCHAR(13) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mother VARCHAR(48) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, id_p_second_tutor VARCHAR(13) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, first_phone INT NOT NULL, second_phone INT NOT NULL, school VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, other_school VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, group_letter VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, language VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, religion VARCHAR(22) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, first_optional INT NOT NULL, second_optional INT NOT NULL, third_optional INT NOT NULL, fourth_optional INT NOT NULL, first_act INT DEFAULT NULL, second_act INT DEFAULT NULL, third_act INT DEFAULT NULL, fourh_act INT DEFAULT NULL, second_first_optional INT DEFAULT NULL, second_second_optional INT DEFAULT NULL, secont_third_optional INT DEFAULT NULL, second_fourth_optional INT DEFAULT NULL, second_first_act INT DEFAULT NULL, second_second_act INT DEFAULT NULL, second_third_act INT DEFAULT NULL, second_fourth_act INT DEFAULT NULL, observations LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, exemption INT DEFAULT NULL, bilingualism VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, course VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date DATETIME NOT NULL, promotes INT DEFAULT NULL, transport INT DEFAULT NULL, east_route VARCHAR(42) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, west_route VARCHAR(42) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, sex VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, brothers INT DEFAULT NULL, nationality VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, itinerary INT DEFAULT NULL, fourth_optionals VARCHAR(32) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, fifth_optional INT DEFAULT NULL, sixth_optional INT DEFAULT NULL, seventh_optional INT DEFAULT NULL, diversificate INT DEFAULT NULL, second_fifth_optional INT DEFAULT NULL, second_sixth_optional INT DEFAULT NULL, second_seventh_optional INT DEFAULT NULL, confirmed INT DEFAULT NULL, administrator INT DEFAULT NULL, actual_group VARCHAR(2) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, revised INT DEFAULT NULL, disease VARCHAR(254) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, other_disease VARCHAR(254) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, photo INT NOT NULL, divorce VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, third_math VARCHAR(1) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fourth_science VARCHAR(1) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }
}
