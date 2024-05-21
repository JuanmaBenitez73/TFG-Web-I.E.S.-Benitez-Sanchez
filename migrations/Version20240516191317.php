<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516191317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE students (id INT AUTO_INCREMENT NOT NULL, student VARCHAR(255) NOT NULL, registration_status VARCHAR(255) DEFAULT NULL, school_id VARCHAR(10) NOT NULL, dni_student VARCHAR(9) NOT NULL, address VARCHAR(255) NOT NULL, postal_code INT NOT NULL, residence_location VARCHAR(255) NOT NULL, birthday DATE NOT NULL, residence_province VARCHAR(255) NOT NULL, phone VARCHAR(9) NOT NULL, emergency_phone VARCHAR(9) NOT NULL, student_personal_phone VARCHAR(9) NOT NULL, student_personal_email VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, course VARCHAR(255) NOT NULL, center_file_number VARCHAR(9) NOT NULL, unit VARCHAR(30) NOT NULL, first_surname VARCHAR(30) NOT NULL, second_surname VARCHAR(30) NOT NULL, name VARCHAR(30) NOT NULL, first_tutor_dni VARCHAR(9) NOT NULL, first_tutor_first_surname VARCHAR(30) NOT NULL, first_tutor_second_surname VARCHAR(30) NOT NULL, first_tutor_name VARCHAR(30) NOT NULL, first_tutor_email VARCHAR(255) NOT NULL, first_tutor_phone VARCHAR(9) NOT NULL, first_tutor_sex VARCHAR(1) NOT NULL, second_tutor_dni VARCHAR(9) DEFAULT NULL, second_tutor_first_surname VARCHAR(30) DEFAULT NULL, second_tutor_second_surname VARCHAR(30) DEFAULT NULL, second_tutor_email VARCHAR(255) DEFAULT NULL, second_tutor_name VARCHAR(30) DEFAULT NULL, second_tutor_sex VARCHAR(1) DEFAULT NULL, second_tutor_phone VARCHAR(9) DEFAULT NULL, born_location VARCHAR(255) NOT NULL, tuition_year INT NOT NULL, tuitions_number_this_course INT NOT NULL, tuition_observations VARCHAR(255) DEFAULT NULL, born_province VARCHAR(255) NOT NULL, born_country VARCHAR(255) NOT NULL, age_last_day_tuition_year INT NOT NULL, nationality VARCHAR(255) NOT NULL, student_sex VARCHAR(1) NOT NULL, tuition_date DATE NOT NULL, social_security_number VARCHAR(255) NOT NULL, have_disease VARCHAR(255) DEFAULT NULL, follow_treatment VARCHAR(255) DEFAULT NULL, medicines_allergy VARCHAR(255) DEFAULT NULL, food_intolerances VARCHAR(255) DEFAULT NULL, custody VARCHAR(255) DEFAULT NULL, large_family VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE students');
    }
}
