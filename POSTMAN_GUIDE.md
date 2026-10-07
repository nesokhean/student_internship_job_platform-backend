# Postman API Testing Guide

## Environment Variables
- `BASE_URL`: http://127.0.0.1:8000/api
- `TOKEN`: Bearer token (after login)

## Authentication Endpoints

### 1. Register Student
POST {{BASE_URL}}/auth/register
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!",
    "role": "student"
}
```

### 2. Register Company
POST {{BASE_URL}}/auth/register
```json
{
    "name": "Acme Corp",
    "email": "hr@acme.com",
    "password": "Password123!",
    "password_confirmation": "Password123!",
    "role": "company"
}
```

### 3. Login
POST {{BASE_URL}}/auth/login
```json
{
    "email": "john@example.com",
    "password": "Password123!"
}
```
Response contains `token` - use in Authorization: Bearer {{TOKEN}}

### 4. Get Current User
GET {{BASE_URL}}/auth/me
Headers: Authorization: Bearer {{TOKEN}}

### 5. Logout
POST {{BASE_URL}}/auth/logout
Headers: Authorization: Bearer {{TOKEN}}

### 6. Logout All
POST {{BASE_URL}}/auth/logout-all
Headers: Authorization: Bearer {{TOKEN}}

### 7. Forgot Password
POST {{BASE_URL}}/auth/forgot-password
```json
{
    "email": "john@example.com"
}
```

### 8. Reset Password
POST {{BASE_URL}}/auth/reset-password
```json
{
    "token": "reset-token-from-email",
    "email": "john@example.com",
    "password": "NewPassword123!",
    "password_confirmation": "NewPassword123!"
}
```

### 9. Resend Verification Email
POST {{BASE_URL}}/auth/email/resend
Headers: Authorization: Bearer {{TOKEN}}

### 10. Verify Email
GET {{BASE_URL}}/auth/email/verify/{id}/{hash}
Headers: Authorization: Bearer {{TOKEN}}

## Public Job Endpoints

### 11. List Jobs (Public)
GET {{BASE_URL}}/jobs
Query params: search, job_type, location, salary_min, salary_max, deadline, page, per_page

### 12. Get Job Details
GET {{BASE_URL}}/jobs/{id}

## Student Endpoints

### 13. Get Student Profile
GET {{BASE_URL}}/student/profile
Headers: Authorization: Bearer {{TOKEN}}

### 14. Create/Update Student Profile
POST/PUT {{BASE_URL}}/student/profile
Headers: Authorization: Bearer {{TOKEN}}
Form-data: phone, date_of_birth, gender, university, major, year, bio, skills, address, profile_image, cv

### 15. Delete Student Profile
DELETE {{BASE_URL}}/student/profile
Headers: Authorization: Bearer {{TOKEN}}

### 16. Upload CV
POST {{BASE_URL}}/student/profile/cv
Headers: Authorization: Bearer {{TOKEN}}
Form-data: cv (pdf/doc/docx)

### 17. Download CV
GET {{BASE_URL}}/student/profile/cv
Headers: Authorization: Bearer {{TOKEN}}

### 18. Delete CV
DELETE {{BASE_URL}}/student/profile/cv
Headers: Authorization: Bearer {{TOKEN}}

### 19. Get Daily Application Limit
GET {{BASE_URL}}/student/applications/daily-limit
Headers: Authorization: Bearer {{TOKEN}}

### 20. List Student Applications
GET {{BASE_URL}}/student/applications
Headers: Authorization: Bearer {{TOKEN}}

### 21. Get Student Application
GET {{BASE_URL}}/student/applications/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 22. Cancel Application (only pending)
DELETE {{BASE_URL}}/student/applications/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 23. Apply for Job
POST {{BASE_URL}}/jobs/{job}/apply
Headers: Authorization: Bearer {{TOKEN}}
Form-data or JSON: cover_letter, cv (optional)

## Company Endpoints

### 24. Get Company Profile
GET {{BASE_URL}}/company/profile
Headers: Authorization: Bearer {{TOKEN}}

### 25. Create/Update Company Profile
POST/PUT {{BASE_URL}}/company/profile
Headers: Authorization: Bearer {{TOKEN}}
Form-data or JSON: company_name, company_email, phone, website, description, industry, location, logo

### 26. Delete Company Profile
DELETE {{BASE_URL}}/company/profile
Headers: Authorization: Bearer {{TOKEN}}

### 27. Create Job Posting
POST {{BASE_URL}}/company/jobs
Headers: Authorization: Bearer {{TOKEN}}
```json
{
    "title": "Software Engineer Internship",
    "description": "Description here",
    "requirements": "PHP, Laravel",
    "responsibilities": "Develop APIs",
    "location": "Phnom Penh",
    "job_type": "internship",
    "salary_min": 300,
    "salary_max": 500,
    "deadline": "2026-12-31",
    "status": "draft"
}
```

### 28. List Company Jobs
GET {{BASE_URL}}/company/jobs
Headers: Authorization: Bearer {{TOKEN}}

### 29. Get Company Job
GET {{BASE_URL}}/company/jobs/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 30. Update Job
PUT {{BASE_URL}}/company/jobs/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 31. Delete Job
DELETE {{BASE_URL}}/company/jobs/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 32. Publish Job
POST {{BASE_URL}}/company/jobs/{id}/publish
Headers: Authorization: Bearer {{TOKEN}}

### 33. Close Job
POST {{BASE_URL}}/company/jobs/{id}/close
Headers: Authorization: Bearer {{TOKEN}}

### 34. List Job Applications
GET {{BASE_URL}}/company/jobs/{job}/applications
Headers: Authorization: Bearer {{TOKEN}}

### 35. Get Application Details
GET {{BASE_URL}}/company/applications/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 36. Update Application Status
PATCH {{BASE_URL}}/company/applications/{id}/status
Headers: Authorization: Bearer {{TOKEN}}
```json
{
    "status": "reviewing"
}
```

## Admin Endpoints

### 37. Admin Dashboard
GET {{BASE_URL}}/admin/dashboard
Headers: Authorization: Bearer {{TOKEN}}

### 38. List Users
GET {{BASE_URL}}/admin/users
Query: search, role, status, page
Headers: Authorization: Bearer {{TOKEN}}

### 39. Get User
GET {{BASE_URL}}/admin/users/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 40. Update User Status
PATCH {{BASE_URL}}/admin/users/{id}/status
Headers: Authorization: Bearer {{TOKEN}}
```json
{
    "status": "blocked"
}
```

### 41. Delete User
DELETE {{BASE_URL}}/admin/users/{id}
Headers: Authorization: Bearer {{TOKEN}}

### 42. List Students
GET {{BASE_URL}}/admin/students
Headers: Authorization: Bearer {{TOKEN}}

### 43. List Companies
GET {{BASE_URL}}/admin/companies
Headers: Authorization: Bearer {{TOKEN}}

### 44. List All Jobs
GET {{BASE_URL}}/admin/jobs
Headers: Authorization: Bearer {{TOKEN}}

### 45. List All Applications
GET {{BASE_URL}}/admin/applications
Headers: Authorization: Bearer {{TOKEN}}
