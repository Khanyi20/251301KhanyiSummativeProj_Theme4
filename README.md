# ![logo](/frontend/assets/Pawsitive%20Animal%20Shelter_logo_cutout%202.png) Pawsitive Animal Shelter

A web application for an animal shelter that was developed using PHP + MySQL. User can Browse animals, Apply to adopt an animal, book shelter visits and record donations form customers anonymously. The website has two sides, the customer side that indludes the dashboard and the staff dashboard where staff members can manage, add and view records. 

**Live Site**: pawsitive-e5c2gqh8deasgef7.southafricanorth-01.azurewebsites.net

---

## Table Of Contents

1. Features
2. Tech Stack
3. Project Structure
4. Design vs Final Build
5. Screenshots
6. Deployment In Azure
7. Running Locally
8. Database
9. Security Notes
10. Limitations
11. Author

---

## 1. Features

Public / landing page
1. About Us, Our Facility (image slideshow), Directors and Committee Members sections
2. Sign In, Sign Up and Donate calls to action


### Users

| Pages | What it does | 
| -------- | -------- |
| signup.php / signin.php | Account creation and login (hashed passwords, session-based)| 
| dashboard.php | Environment with shorstcuts to other pages: Meet the Animals, Book a Visit, Make a Donation, My Applications, My Bookings | 
| animals.php | Browse animals available for adoption | 
| apply.php | Adoption application form | 
| applications.php | Track the status of your adoption applications | 
| book_visit.php | Request a shelter visit (date, time, number of visitors, etc.) | 
| mybookings.php | View your visit bookings and their status | 
| donate.php | Record a donation (amount in ZAR, once-off or monthly option, optional message) | 
| client_donations.php | View your donation history | 

### Staff

| Pages | What it does | 
| -------- | -------- |
| staff_dashboard.php | Overview with recent adoption applications and recent donations | 
| manage_animals.php | Add animals (with image upload), edit, filter and delete |
| manage_applications.php | Review applications and update their status |
| manage_bookings.php | Review visit bookings and update their status |
| manage_donations.php | Review donations and update their status |

---

## 2. Tech Stack

- Backend: PHP (PDO with prepared statements)
- Database: MySQL (Azure Database for MySQL Flexible Server)
- Frontend: HTML, CSS (separate stylesheets for common, staff, customer, login and main pages) and JavaScript
- Hosting: Azure App Service (Linux, PHP)
- CI/CD: GitHub Actions = Azure Web App deploy
- Analytics / SEO: Google Analytics, sitemap.xml, Google Search Console verification file

---

## 3. Project Structure

.
├── index.php                  # Entry point, loads the landing page
├── backend/
│   └── db.php                 # PDO connection (reads credentials from environment variables)
├── frontend/
│   ├── pages/                 # All PHP pages (public, user and staff)
│   ├── css/                   # common, main_styles, login_style, customer, staff
│   ├── Javascript/script.js   # Responsive nav toggle + slideshow
│   └── assets/                # Logo, slideshow images, staff images, patterns
├── .github/workflows/         # Azure deployment workflow(s)
├── sitemap.xml
└── README.md

---

## 4. Design vs Final Build

| Design               | Final Look               |
| ---------------------- | ---------------------- |
| ![old](/frontend/readme_assets/old_landing.png) | ![new](/frontend/readme_assets/landingpage.jpeg) |



| Design               | Final Look               |
| ---------------------- | ---------------------- |
| ![old](/frontend/readme_assets/Frame%20383.png) | ![new](/frontend/readme_assets/staff_dashboard.jpeg) |


---

## 5. Screenshot

| Desktop               | Tablet               |Mobile               |
| ---------------------- | ---------------------- | ---------------------- |
| ![old](/frontend/readme_assets/landingpage.jpeg) | ![new](/frontend/readme_assets/tablet_landing.jpeg) | ![new](/frontend/readme_assets/mobile_landing.jpeg) |

---

## 6. Deployement in Azure

The site is deployed entirely on Microsoft Azure.

Component	= Azure service
- Web application	= Azure App Service (Web App named pawsitive, Production slot, region South Africa North)
- Database	= Azure Database for MySQL – Flexible Server
- CI/CD = GitHub Actions using azure/login (OpenID Connect) and azure/webapps-deploy
