# MMGC Hospital

MMGC-Medical-Management-General-Care-System is a medical management application covering patient and clinic workflows.

![PHP](https://img.shields.io/badge/PHP-2563EB?style=flat-square) ![MySQL](https://img.shields.io/badge/MySQL-0F172A?style=flat-square)

## Overview

The data model covers `admins`, `appointments`, `doctors`, `doctor_schedules`, `invoices`, `lab_categories`, `lab_reports`, `lab_tests`, `patients`, `patient_vitals`, `payments`, `procedures`, and related records.

## Application areas

| Area | Pages in the repository |
| --- | --- |
| Accounts | [`accounts/accounts_dashboard.php`](accounts/accounts_dashboard.php), [`accounts/add_invoice.php`](accounts/add_invoice.php), [`accounts/add_payment.php`](accounts/add_payment.php), [`accounts/edit_invoice.php`](accounts/edit_invoice.php), [`accounts/edit_payment.php`](accounts/edit_payment.php) |
| Admin | [`admin/add_appointment.php`](admin/add_appointment.php), [`admin/add_doctor.php`](admin/add_doctor.php), [`admin/add_invoice.php`](admin/add_invoice.php), [`admin/add_lab_category.php`](admin/add_lab_category.php), [`admin/add_lab_report.php`](admin/add_lab_report.php) |
| Doctor | [`doctor/add_lab_report_doctor.php`](doctor/add_lab_report_doctor.php), [`doctor/add_procedure.php`](doctor/add_procedure.php), [`doctor/appointments.php`](doctor/appointments.php), [`doctor/doctor_dashboard.php`](doctor/doctor_dashboard.php), [`doctor/edit_lab_report_doctor.php`](doctor/edit_lab_report_doctor.php) |
| Root Pages | [`doctors.php`](doctors.php), [`forgot_password.php`](forgot_password.php), [`login.php`](login.php), [`register.php`](register.php) |
| Lab | [`lab/add_lab_category.php`](lab/add_lab_category.php), [`lab/add_lab_report.php`](lab/add_lab_report.php), [`lab/add_lab_test.php`](lab/add_lab_test.php), [`lab/edit_lab_category.php`](lab/edit_lab_category.php), [`lab/edit_lab_report.php`](lab/edit_lab_report.php) |
| Nurse | [`nurse/add_progress.php`](nurse/add_progress.php), [`nurse/edit_procedure.php`](nurse/edit_procedure.php), [`nurse/nurse_dashboard.php`](nurse/nurse_dashboard.php), [`nurse/patients.php`](nurse/patients.php), [`nurse/view_procedures.php`](nurse/view_procedures.php) |
| Patient | [`patient/appointments_history.php`](patient/appointments_history.php), [`patient/book_test.php`](patient/book_test.php), [`patient/doctors.php`](patient/doctors.php), [`patient/downloads.php`](patient/downloads.php), [`patient/generate_invoice.php`](patient/generate_invoice.php) |
| Reception | [`reception/add_appointment.php`](reception/add_appointment.php), [`reception/edit_appointment.php`](reception/edit_appointment.php), [`reception/reception_add_patient.php`](reception/reception_add_patient.php), [`reception/reception_dashboard.php`](reception/reception_dashboard.php), [`reception/reception_edit_patient.php`](reception/reception_edit_patient.php) |

## Getting started

### 1. Get the project

```bash
git clone https://github.com/nasirabbas827/MMGC-Medical-Management-General-Care-System.git
cd MMGC-Medical-Management-General-Care-System
```

### 2. Configure PHP and the database

1. Use a local PHP server with MySQL or MariaDB, such as XAMPP.
2. Import [`Database/mmgc_db.sql`](Database/mmgc_db.sql) into your local database server.
3. Set your local database connection details in [`accounts/config.php`](accounts/config.php), [`admin/config.php`](admin/config.php), [`config.php`](config.php), [`doctor/config.php`](doctor/config.php), [`lab/config.php`](lab/config.php), [`nurse/config.php`](nurse/config.php), [`patient/config.php`](patient/config.php), [`reception/config.php`](reception/config.php).
4. Keep the existing asset and upload directory paths intact.

### 3. Start the application

With the database running, start a local development server from the repository root:

```bash
php -S 127.0.0.1:8000
```

Open http://127.0.0.1:8000/index.php. If using Apache/XAMPP instead, place the project in its document root and open the matching localhost folder.

## Repository guide

| Path | Purpose |
| --- | --- |
| [`Database`](Database) | Database scripts |
| [`PHPMailer`](PHPMailer) | Bundled mail integration |
| [`config.php`](config.php) | Application/database configuration |
| [`css`](css) | Stylesheets |
| [`images`](images) | Image assets |
| [`index.php`](index.php) | Web entry page |

## Development notes

These setup instructions are based on the repository source. The application has not been executed as part of this documentation update; verify dependencies and configuration in your local environment.
