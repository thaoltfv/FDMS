# User Management System

## 👥 Team & Permission Management

### Screen 1: User Management

```
┌─────────────────────────────────────────┐
│  ← Back          Team               ➕  │
│                                         │
│  ┌─────────────────────────────────────┐│
│  │ 🔍 Search team members...           ││
│  └─────────────────────────────────────┘│
│                                         │
│  🎯 Filter: All | 🟢 Active | ⏸️ Pending │
│                                         │
│  👥 Team Members (24)                   │
│  ┌─────────────────────────────────────┐│
│  │ 👤 John Smith                       ││
│  │ 📧 john@company.com                 ││
│  │ 🎯 Data Manager • 🟢 Active         ││
│  │ 🏢 HR Department                    ││
│  │                     ⚙️ Manage      ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 👤 Sarah Johnson                    ││
│  │ 📧 sarah@company.com                ││
│  │ 🎯 Blueprint Admin • 🟢 Active      ││
│  │ 🏢 IT Department                    ││
│  │                     ⚙️ Manage      ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 👤 Mike Davis                       ││
│  │ 📧 mike@company.com                 ││
│  │ 🎯 Viewer • 🟡 Pending              ││
│  │ 🏢 Finance Team                     ││
│  │                     ⚙️ Manage      ││
│  └─────────────────────────────────────┘│
│                                         │
│  📊 Total: 24 users                     │
│  🔄 Last sync: 5 minutes ago            │
│                                         │
│  ┌─────────────────────────────────────┐│
│  │         📧 Invite New User          ││
│  └─────────────────────────────────────┘│
│                                         │
│ ════════════════════════════════════════│
│ 🏠 Home  📋 Blueprints  📄 Docs  👤 More│
└─────────────────────────────────────────┘
```

### Screen 2: Role & Group Management

```
┌─────────────────────────────────────────┐
│  ← Back      Roles & Groups         ➕  │
│                                         │
│  🎯 System Roles                        │
│  ┌─────────────────────────────────────┐│
│  │ 👑 Super Admin                 (2)  ││
│  │ Full system access                  ││
│  │ ✅ All permissions                  ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 🔧 Blueprint Admin             (5)  ││
│  │ Create and manage blueprints        ││
│  │ 📋 Schema management                ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📝 Data Manager               (12)  ││
│  │ Create and edit documents           ││
│  │ 📄 Document operations              ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 👁️ Viewer                      (15)  ││
│  │ Read-only access                    ││
│  │ 📖 View documents only              ││
│  └─────────────────────────────────────┘│
│                                         │
│  👥 Organizational Groups               │
│  ┌─────────────────────────────────────┐│
│  │ 🏢 HR Department               (8)  ││
│  │ Human resources team                ││
│  │ 📄 Employee documents               ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 💻 IT Department               (6)  ││
│  │ Information technology              ││
│  │ 🔧 Asset management                 ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 💰 Finance Team                (4)  ││
│  │ Financial operations                ││
│  │ 💰 Expense reports                  ││
│  └─────────────────────────────────────┘│
│                                         │
│  ⚙️ Management                          │
│  ┌─────────────────────────────────────┐│
│  │ ➕ Create Group                     ││
│  │ 📊 Permission Matrix                ││
│  │ 🔄 Import Users                     ││
│  └─────────────────────────────────────┘│
└─────────────────────────────────────────┘
```

### Screen 3: Permission Matrix

```
┌─────────────────────────────────────────┐
│  ← Back      Permissions            ✅  │
│                                         │
│  🔐 Blueprint: Employee Registration    │
│                                         │
│  👥 Group Permissions                   │
│  ┌─────────────────────────────────────┐│
│  │ 🏢 HR Department                    ││
│  │ ✅ Read    ✅ Create   ✅ Edit       ││
│  │ ❌ Delete  ✅ Approve               ││
│  │ 📋 Stages: Draft, Review            ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📊 Management                       ││
│  │ ✅ Read All  ❌ Create  ❌ Edit      ││
│  │ ❌ Delete    ✅ Final Approve       ││
│  │ 📋 Stages: Review, Approved         ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 👁️ Employees                        ││
│  │ ✅ Read Own  ✅ Create Own  ❌ Edit  ││
│  │ ❌ Delete    ❌ Approve             ││
│  │ 📋 Stages: Draft only               ││
│  └─────────────────────────────────────┘│
│                                         │
│  🔸 Section Permissions                 │
│  ┌─────────────────────────────────────┐│
│  │ Basic Information                   ││
│  │ 👥 All groups: View & Edit          ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ Employment Details                  ││
│  │ 👥 HR & Management only             ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ Salary Information                  ││
│  │ 👥 Management & Payroll only        ││
│  └─────────────────────────────────────┘│
│                                         │
│  🎯 Quick Actions                       │
│  ┌─────────────────────────────────────┐│
│  │ 📋 Copy from Template               ││
│  │ 🔄 Reset to Defaults                ││
│  │ 📤 Export Permissions               ││
│  └─────────────────────────────────────┘│
└─────────────────────────────────────────┘
```

## 📧 User Invitation Process

### Invite New User

```
┌─────────────────────────────────────────┐
│  ← Back       Invite User           📧  │
│                                         │
│  👤 User Information                    │
│  ┌─────────────────────────────────────┐│
│  │ 📧 Email Address *                  ││
│  │ new.user@company.com                ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 👤 Full Name *                      ││
│  │ Jane Doe                            ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 🏢 Department                       ││
│  │ Select Department               ▼   ││
│  └─────────────────────────────────────┘│
│                                         │
│  🎯 Role Assignment                     │
│  ┌─────────────────────────────────────┐│
│  │ ○ Super Admin                       ││
│  │ ○ Blueprint Admin                   ││
│  │ ● Data Manager                      ││
│  │ ○ Viewer                            ││
│  │ ○ Auditor                           ││
│  └─────────────────────────────────────┘│
│                                         │
│  👥 Group Membership                    │
│  ┌─────────────────────────────────────┐│
│  │ ☑️ HR Department                    ││
│  │ ☐ IT Department                     ││
│  │ ☑️ New Employee Onboarding          ││
│  │ ☐ Finance Team                      ││
│  │ ☐ Management                        ││
│  └─────────────────────────────────────┘│
│                                         │
│  📧 Invitation Message                  │
│  ┌─────────────────────────────────────┐│
│  │ Welcome to FDMS! You've been        ││
│  │ invited to join our document        ││
│  │ management system...                ││
│  └─────────────────────────────────────┘│
│                                         │
│  ⚙️ Settings                            │
│  ☑️ Require password change on first login│
│  ☑️ Send welcome email                  │
│  ☐ Set account expiration date          │
│                                         │
│  ┌─────────────────────────────────────┐│
│  │         📧 Send Invitation          ││
│  └─────────────────────────────────────┘│
└─────────────────────────────────────────┘
```

## 🔐 Advanced Permission Features

### Individual User Permissions

```
┌─────────────────────────────────────────┐
│  ← Back      John Smith             ⚙️  │
│                                         │
│  👤 User Profile                        │
│  📧 john.smith@company.com              │
│  🎯 Data Manager • 🟢 Active            │
│  🏢 HR Department                       │
│                                         │
│  📋 Document Access                     │
│  ┌─────────────────────────────────────┐│
│  │ 📄 Employee Registration            ││
│  │ ✅ Create ✅ Read ✅ Edit ❌ Delete  ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📄 Asset Management                 ││
│  │ ❌ Create ✅ Read ❌ Edit ❌ Delete  ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📄 Expense Reports                  ││
│  │ ✅ Create ✅ Read ✅ Edit ❌ Delete  ││
│  └─────────────────────────────────────┘│
│                                         │
│  👥 Group Memberships                   │
│  ┌─────────────────────────────────────┐│
│  │ 🏢 HR Department             👑     ││
│  │ 📅 Member since: Jan 2024           ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📊 New Employee Onboarding          ││
│  │ 📅 Member since: Jan 2024           ││
│  └─────────────────────────────────────┘│
│                                         │
│  📊 Activity Summary                    │
│  ┌─────────────────────────────────────┐│
│  │ 📄 Documents Created: 15            ││
│  │ 📝 Documents Edited: 23             ││
│  │ 👀 Last Active: 2 hours ago        ││
│  │ 📅 Member Since: Jan 15, 2024       ││
│  └─────────────────────────────────────┘│
│                                         │
│  ⚙️ User Actions                        │
│  ┌─────────────────────────────────────┐│
│  │ ✏️ Edit User Details                ││
│  │ 🔐 Reset Password                   ││
│  │ ⏸️ Suspend Account                   ││
│  │ 🗑️ Deactivate User                  ││
│  └─────────────────────────────────────┘│
└─────────────────────────────────────────┘
```

## 📊 User Analytics & Reporting

### Team Performance Dashboard

```
┌─────────────────────────────────────────┐
│  ← Back      Team Analytics         📊  │
│                                         │
│  📈 Team Overview                       │
│  ┌─────────────────────────────────────┐│
│  │ 👥 Total Users: 24                  ││
│  │ 🟢 Active: 22  🟡 Pending: 2        ││
│  │ 📊 Documents: 156 (↑ 12 this week)  ││
│  │ ⏱️ Avg Response: 4.2 hours          ││
│  └─────────────────────────────────────┘│
│                                         │
│  📋 Department Activity                 │
│  ┌─────────────────────────────────────┐│
│  │ 🏢 HR Department                    ││
│  │ 📄 45 documents • 👥 8 users        ││
│  │ 📊 ████████░░ 80% completion        ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 💻 IT Department                    ││
│  │ 📄 32 documents • 👥 6 users        ││
│  │ 📊 ██████░░░░ 60% completion        ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 💰 Finance Team                     ││
│  │ 📄 28 documents • 👥 4 users        ││
│  │ 📊 ███████░░░ 70% completion        ││
│  └─────────────────────────────────────┘│
│                                         │
│  📊 Top Performers                      │
│  ┌─────────────────────────────────────┐│
│  │ 🥇 Sarah Johnson • 23 documents     ││
│  │ 🥈 Mike Davis • 19 documents        ││
│  │ 🥉 John Smith • 15 documents        ││
│  └─────────────────────────────────────┘│
│                                         │
│  ⚙️ Report Actions                      │
│  ┌─────────────────────────────────────┐│
│  │ 📤 Export Report                    ││
│  │ 📧 Email to Management              ││
│  │ 📅 Schedule Weekly Report           ││
│  │ 🎯 Set Team Goals                   ││
│  └─────────────────────────────────────┘│
└─────────────────────────────────────────┘
```

**Key Features:**
- Comprehensive user management
- Role-based access control
- Multi-level permission matrix
- Team performance analytics
- Invitation and onboarding workflow
- Activity tracking and reporting 