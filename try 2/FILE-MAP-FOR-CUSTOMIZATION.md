# 📁 File Map for Quick Customization

## 🎯 Files You Need to Edit (Only 4 Files!)

```
project/
├── frontend/
│   └── src/
│       ├── views/
│       │   └── LoginView.vue ⭐ EDIT THIS (1 line)
│       ├── components/
│       │   └── AdminLayout.vue ⭐ EDIT THIS (3 sections)
│       └── views/
│           └── RequestsView.vue ⭐ EDIT THIS (4 labels)
│
└── backend/
    └── database/
        └── seeders/
            └── UserSeeder.php ⭐ EDIT THIS (1 line)
```

---

## 📝 Detailed Edit Locations

### File 1: `frontend/src/views/LoginView.vue`

**Location:** Line 4  
**Current:**
```vue
<h1>Request & Calculation Workflow System</h1>
```

**Change to:**
```vue
<!-- Hospital -->
<h1>Hospital Appointment System</h1>

<!-- School -->
<h1>School Management System</h1>

<!-- Work -->
<h1>Work Task Management System</h1>
```

**Time:** 10 seconds

---

### File 2: `frontend/src/components/AdminLayout.vue`

#### Section 1: Sidebar Title
**Location:** Line 5  
**Current:**
```vue
<h2>Workflow System</h2>
```

**Change to:**
```vue
<!-- Hospital -->
<h2>Hospital System</h2>

<!-- School -->
<h2>School System</h2>

<!-- Work -->
<h2>Work System</h2>
```

**Time:** 10 seconds

---

#### Section 2: Role Display
**Location:** Lines 42-43  
**Current:**
```vue
<span class="user-role" :class="authStore.user?.role">
  {{ authStore.user?.role }}
</span>
```

**Change to:**
```vue
<!-- Hospital -->
<span class="user-role" :class="authStore.user?.role">
  {{ authStore.user?.role === 'admin' ? 'Doctor' : 'Patient' }}
</span>

<!-- School -->
<span class="user-role" :class="authStore.user?.role">
  {{ authStore.user?.role === 'admin' ? 'Officer' : 'Student' }}
</span>

<!-- Work -->
<span class="user-role" :class="authStore.user?.role">
  {{ authStore.user?.role === 'admin' ? 'Manager' : 'Employee' }}
</span>
```

**Time:** 30 seconds

---

#### Section 3: Navigation Menu
**Location:** Lines 10-30  
**Current:**
```vue
<router-link to="/requests" class="nav-item" active-class="active">
  <span class="icon">📝</span>
  My Requests
</router-link>
<router-link to="/admin/requests" class="nav-item" active-class="active">
  <span class="icon">📋</span>
  All Requests
</router-link>
<router-link to="/admin/users" class="nav-item" active-class="active">
  <span class="icon">👥</span>
  Users
</router-link>
```

**Change to:**
```vue
<!-- Hospital -->
<router-link to="/requests" class="nav-item" active-class="active">
  <span class="icon">📅</span>
  My Appointments
</router-link>
<router-link to="/admin/requests" class="nav-item" active-class="active">
  <span class="icon">🏥</span>
  All Appointments
</router-link>
<router-link to="/admin/users" class="nav-item" active-class="active">
  <span class="icon">👥</span>
  Patients
</router-link>

<!-- School -->
<router-link to="/requests" class="nav-item" active-class="active">
  <span class="icon">📚</span>
  My Requests
</router-link>
<router-link to="/admin/requests" class="nav-item" active-class="active">
  <span class="icon">📋</span>
  All Requests
</router-link>
<router-link to="/admin/users" class="nav-item" active-class="active">
  <span class="icon">👥</span>
  Students
</router-link>

<!-- Work -->
<router-link to="/requests" class="nav-item" active-class="active">
  <span class="icon">✅</span>
  My Tasks
</router-link>
<router-link to="/admin/requests" class="nav-item" active-class="active">
  <span class="icon">📊</span>
  All Tasks
</router-link>
<router-link to="/admin/users" class="nav-item" active-class="active">
  <span class="icon">👥</span>
  Employees
</router-link>
```

**Time:** 1 minute

---

### File 3: `frontend/src/views/RequestsView.vue`

**Location:** Lines 60-90 (Create Modal Form)  
**Current:**
```vue
<label for="title">Title</label>
<label for="description">Description</label>
<label for="quantity">Quantity *</label>
<label for="unit_price">Unit Price ($) *</label>
```

**Change to:**
```vue
<!-- Hospital -->
<label for="title">Appointment Type</label>
<label for="description">Symptoms/Notes</label>
<label for="quantity">Duration (hours) *</label>
<label for="unit_price">Fee per Hour ($) *</label>

<!-- School -->
<label for="title">Request Type</label>
<label for="description">Details</label>
<label for="quantity">Items/Credits *</label>
<label for="unit_price">Cost per Item ($) *</label>

<!-- Work -->
<label for="title">Task Name</label>
<label for="description">Task Description</label>
<label for="quantity">Hours *</label>
<label for="unit_price">Rate per Hour ($) *</label>
```

**Also change in Edit Modal (Lines 120-150)** - Same labels

**Time:** 2 minutes

---

### File 4: `backend/database/seeders/UserSeeder.php`

**Location:** Line 30  
**Current:**
```php
SystemSetting::set('registration_mode', 'public');
```

**Change to:**
```php
// Hospital (patients can register)
SystemSetting::set('registration_mode', 'public');

// School (only officers can add students)
SystemSetting::set('registration_mode', 'admin_only');

// Work (only managers can add employees)
SystemSetting::set('registration_mode', 'admin_only');
```

**Time:** 10 seconds

---

## 🎯 Optional: Admin Request View

### File: `frontend/src/views/Admin/RequestsView.vue`

**Location:** Lines 100-150 (View Modal)  
**Current:**
```vue
<label>Title:</label>
<label>Description:</label>
<label>Quantity:</label>
<label>Unit Price:</label>
```

**Change to match your scenario** (same as RequestsView.vue)

**Time:** 1 minute (optional)

---

## 📊 Visual File Structure

```
🎯 MUST EDIT (4 files)
├── 📄 LoginView.vue (1 line)
├── 📄 AdminLayout.vue (3 sections)
├── 📄 RequestsView.vue (4 labels × 2 modals)
└── 📄 UserSeeder.php (1 line)

⚙️ OPTIONAL EDIT (1 file)
└── 📄 Admin/RequestsView.vue (4 labels)

❌ DON'T TOUCH (Everything else)
├── 📁 Models/
├── 📁 Controllers/
├── 📁 Migrations/
├── 📁 Routes/
└── 📁 Services/
```

---

## 🔍 How to Find Files Quickly

### Using VS Code:
1. Press `Ctrl+P` (Quick Open)
2. Type filename
3. Press Enter

### File Paths:
```
LoginView.vue
→ Ctrl+P → type "loginview"

AdminLayout.vue
→ Ctrl+P → type "adminlayout"

RequestsView.vue
→ Ctrl+P → type "requestsview"

UserSeeder.php
→ Ctrl+P → type "userseeder"
```

---

## 📝 Edit Checklist

### Before Competition:
- [ ] Open all 4 files in tabs
- [ ] Bookmark line numbers
- [ ] Test copy-paste from templates
- [ ] Practice switching between files
- [ ] Time yourself (should be < 5 minutes)

### During Competition:
- [ ] File 1: LoginView.vue → Change title
- [ ] File 2: AdminLayout.vue → Change 3 sections
- [ ] File 3: RequestsView.vue → Change labels
- [ ] File 4: UserSeeder.php → Set registration mode
- [ ] Save all files (Ctrl+K S)
- [ ] Build frontend
- [ ] Reset database
- [ ] Test

---

## 🚀 Quick Commands After Editing

```bash
# 1. Save all files in VS Code
Ctrl+K S

# 2. Build frontend
cd frontend
npm run build

# 3. Reset database
cd backend
php artisan migrate:fresh
php artisan db:seed

# 4. Start servers
# Terminal 1
cd backend
php artisan serve

# Terminal 2
cd frontend
npm run dev

# 5. Test
http://localhost:5173
```

---

## 🎨 Color Customization (Bonus)

### File: `frontend/src/views/LoginView.vue`

**Location:** Line 88 (in `<style scoped>`)  
**Current:**
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

**Change to:**
```css
/* Hospital (Blue/Cyan) */
background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);

/* School (Orange/Yellow) */
background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);

/* Work (Purple/Blue) - Keep current */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

**Time:** 10 seconds (optional)

---

## 📋 Complete Example: Hospital System

### Step-by-Step:

1. **Open LoginView.vue** (Ctrl+P → loginview)
   - Line 4: Change to `<h1>Hospital Appointment System</h1>`

2. **Open AdminLayout.vue** (Ctrl+P → adminlayout)
   - Line 5: Change to `<h2>Hospital System</h2>`
   - Lines 42-43: Change to `{{ authStore.user?.role === 'admin' ? 'Doctor' : 'Patient' }}`
   - Lines 10-30: Change icons and text:
     - `📅 My Appointments`
     - `🏥 All Appointments`
     - `👥 Patients`

3. **Open RequestsView.vue** (Ctrl+P → requestsview)
   - Lines 60-90: Change labels:
     - `Appointment Type`
     - `Symptoms/Notes`
     - `Duration (hours)`
     - `Fee per Hour ($)`
   - Lines 120-150: Same changes for Edit Modal

4. **Open UserSeeder.php** (Ctrl+P → userseeder)
   - Line 30: Keep `SystemSetting::set('registration_mode', 'public');`

5. **Save All** (Ctrl+K S)

6. **Build & Run**
   ```bash
   cd frontend && npm run build
   cd backend && php artisan migrate:fresh && php artisan db:seed
   cd backend && php artisan serve
   cd frontend && npm run dev
   ```

**Total Time: 5 minutes**

---

## 🎯 Pro Tips

### Tip 1: Use Multi-Cursor
In VS Code, select text → `Ctrl+D` to select next occurrence → Edit all at once

### Tip 2: Use Find & Replace
`Ctrl+H` → Find "My Requests" → Replace with "My Appointments" → Replace All

### Tip 3: Keep Templates Open
Open `TEMPLATE-SCENARIOS.md` in split view → Copy-paste directly

### Tip 4: Use Snippets
Create VS Code snippets for common changes

### Tip 5: Practice
Time yourself 3 times before competition → Should get under 5 minutes

---

## ✅ Verification After Editing

### Visual Check:
- [ ] Login page shows correct title
- [ ] Sidebar shows correct system name
- [ ] Navigation menu shows correct labels
- [ ] User role displays correctly
- [ ] Form labels are updated
- [ ] Registration mode is correct

### Functional Check:
- [ ] Can login
- [ ] Can create request/appointment/task
- [ ] Can view list
- [ ] Admin can approve/reject
- [ ] Calculations work
- [ ] Status updates correctly

---

## 🎁 Backup Strategy

### Before Competition:
```bash
# Create backup
cp -r project/ project-backup/

# Or use Git
git add .
git commit -m "Original version"
git branch hospital-system
git branch school-system
git branch work-system
```

### During Competition:
If something breaks, restore from backup:
```bash
# Copy backup
cp -r project-backup/ project/

# Or use Git
git checkout main
```

---

**🎯 You now have a complete map of what to edit!**

Focus on these 4 files, practice the workflow, and you'll be able to customize the system in under 5 minutes during your competition!
