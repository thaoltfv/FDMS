# Dashboard & Navigation System

## 📱 Main Dashboard Interface

### Screen 1: Home Dashboard

```
┌─────────────────────────────────────────┐
│  FDMS                          👤 Profile│
│  Good morning, John! 🌅                  │
│                                         │
│  ┌─────────────────────────────────────┐│
│  │ 📊 Quick Stats                      ││
│  │ ─────────────────────────────────── ││
│  │  📄     ⏳     📋     👥           ││
│  │   24      8      5     12          ││
│  │  Docs  Pending  Types  Team        ││
│  └─────────────────────────────────────┘│
│                                         │
│  🚀 Quick Actions                       │
│  ┌─────────────────────────────────────┐│
│  │ ➕ Create Document                  ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📋 My Blueprints                    ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 🔍 Search Documents                 ││
│  └─────────────────────────────────────┘│
│                                         │
│  📝 Recent Activity (Pull to refresh)   │
│  ┌─────────────────────────────────────┐│
│  │ 📄 EMP-001 submitted for review     ││
│  │ 👤 by Sarah J. • 2 hours ago       ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ ✅ AST-156 approved                 ││
│  │ 👤 by Mike D. • 4 hours ago        ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📋 New blueprint: Expense Reports   ││
│  │ 👤 by Admin • Yesterday             ││
│  └─────────────────────────────────────┘│
│                                         │
│ ════════════════════════════════════════│
│ 🏠 Home  📋 Blueprints  📄 Docs  👤 More│
└─────────────────────────────────────────┘
```

**Key Elements:**
- **Header**: Personalized greeting with profile access
- **Stats Cards**: Overview of user's documents and activity
- **Quick Actions**: Primary tasks users perform frequently
- **Activity Feed**: Real-time updates with pull-to-refresh
- **Bottom Navigation**: Primary app navigation tabs

**Ionic Components:**
- `ion-header` with `ion-toolbar`
- `ion-card` for stats and quick actions
- `ion-list` with `ion-item` for activity feed
- `ion-tab-bar` with `ion-tab-button` for navigation
- `ion-refresher` for pull-to-refresh functionality

---

### Screen 2: Bottom Navigation System

```
┌─────────────────────────────────────────┐
│              Main Content               │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│                                         │
│ ════════════════════════════════════════│
│  🏠      📋        📄      👥     ⚙️   │
│ Home  Blueprints  Docs   Users Settings │
│  ●                                      │
└─────────────────────────────────────────┘
```

**Tab Structure:**

#### 🏠 Home Tab
- **Purpose**: Dashboard overview and quick actions
- **Badge**: None (always accessible)
- **Content**: Stats, recent activity, quick actions

#### 📋 Blueprints Tab
- **Purpose**: Blueprint creation and management
- **Badge**: Count of draft blueprints
- **Access**: Blueprint Admin, Super Admin

#### 📄 Documents Tab  
- **Purpose**: Document listing and management
- **Badge**: Count of pending documents
- **Access**: All authenticated users (filtered by permissions)

#### 👥 Users Tab
- **Purpose**: Team management and permissions
- **Badge**: Count of pending user invitations
- **Access**: Admin roles only

#### ⚙️ Settings Tab
- **Purpose**: App settings and configuration
- **Badge**: Update notifications
- **Access**: All users (features filtered by role)

**Navigation States:**
- **Active Tab**: Blue highlight with filled icon
- **Inactive Tabs**: Gray with outline icons
- **Badge Indicators**: Red circles with white numbers
- **Tab Press**: Slight scale animation with haptic feedback

---

### Screen 3: Global Search Interface

```
┌─────────────────────────────────────────┐
│  ← Back          Search              🎯 │
│                                         │
│  ┌─────────────────────────────────────┐│
│  │ 🔍 Search documents, blueprints...  ││
│  │                                 ✖️  ││
│  └─────────────────────────────────────┘│
│                                         │
│  🎯 Filters                            │
│  ┌─────────────────────────────────────┐│
│  │  📋    📄    👤    📅    🏷️      ││
│  │ Type  Status User  Date  Tags     ││
│  └─────────────────────────────────────┘│
│                                         │
│  📊 Results (24 found)                 │
│  ┌─────────────────────────────────────┐│
│  │ 📄 Employee Registration - EMP-001  ││
│  │ 👤 Sarah Johnson • 🟡 Draft        ││
│  │ 📅 Dec 15, 2024 • HR Department    ││
│  │ ........................employee reg│
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📄 Asset Management - AST-156      ││
│  │ 👤 Mike Davis • ✅ Approved        ││
│  │ 📅 Dec 10, 2024 • IT Department    ││
│  │ ........................asset laptop│
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │ 📋 Expense Report Blueprint        ││
│  │ 👤 Admin • 🟢 Active               ││
│  │ 📅 Dec 12, 2024 • Finance Dept     ││
│  │ ........................expense     │
│  └─────────────────────────────────────┘│
│                                         │
│        Load More Results (20/100)       │
│                                         │
│ ════════════════════════════════════════│
│ 🏠 Home  📋 Blueprints  📄 Docs  👤 More│
└─────────────────────────────────────────┘
```

**Search Features:**
- **Auto-complete**: Suggestions as user types
- **Voice Search**: Microphone icon for speech input
- **Filter Options**: Quick filter buttons
- **Result Previews**: Snippet of matching content
- **Infinite Scroll**: Load more results automatically

**Filter Categories:**
- **Type**: Documents, Blueprints, Users
- **Status**: Draft, Review, Approved, Rejected
- **User**: Created by, Assigned to
- **Date**: Today, This week, This month, Custom range
- **Tags**: Custom tags and categories

---

## 🧭 Navigation Patterns

### Hierarchical Navigation
```
Home → Blueprints → Blueprint Details → Field Configuration
  ↳ Back navigation with breadcrumbs
  ↳ Consistent header with back arrow
  ↳ Context preservation
```

### Tab Navigation
```
Bottom tabs remain persistent across screens
↳ Current tab highlighted
↳ Badge notifications on tabs
↳ Quick switching between contexts
```

### Modal Navigation
```
Full-screen modals for:
↳ Document creation/editing
↳ Blueprint designer
↳ User profile editing
↳ Settings configuration
```

---

## 📊 Dashboard Widgets

### Quick Stats Card
```javascript
// Example configuration
const statsConfig = {
  documents: { count: 24, trend: "+3 this week" },
  pending: { count: 8, priority: "high" },
  blueprints: { count: 5, active: 5 },
  team: { count: 12, online: 8 }
}
```

### Activity Feed
- **Real-time updates** via WebSocket connection
- **Pull-to-refresh** for manual sync
- **Pagination** for performance (20 items initially)
- **Filter options** (my activity, team activity, all)

### Quick Actions
- **Contextual actions** based on user role
- **Recent templates** for quick document creation
- **Bookmarked blueprints** for frequent use
- **Pending tasks** requiring user attention

---

## 🎨 Visual Design Specifications

### Header Design
- **Height**: 56px on mobile (standard toolbar height)
- **Background**: White with subtle shadow
- **Title**: 18px bold, left-aligned
- **Actions**: Icon buttons, 48px touch targets

### Card Components
- **Elevation**: 2dp shadow for depth
- **Radius**: 8px rounded corners
- **Padding**: 16px internal padding
- **Spacing**: 16px between cards

### Color Coding System
- **Blue (#0066CC)**: Primary actions and links
- **Green (#28A745)**: Success states and approved items
- **Orange (#FFA500)**: Pending and warning states
- **Red (#DC3545)**: Errors and rejected items
- **Gray (#6C757D)**: Secondary text and inactive states

---

## 📱 Responsive Behavior

### Portrait Mode (Primary)
- **Single column layout**
- **Full-width cards and buttons**
- **Bottom navigation always visible**
- **Optimized for one-handed use**

### Landscape Mode
- **Two-column layout for tablets**
- **Side navigation for larger screens**
- **Optimized content density**
- **Maintained touch targets**

### Tablet Adaptations
- **Master-detail layout** where appropriate
- **Increased content density**
- **Additional quick actions**
- **Split-screen compatibility**

---

## 🔔 Notification Integration

### Push Notifications
- **Document status changes**
- **New document assignments**
- **Blueprint updates**
- **System maintenance alerts**

### In-App Notifications
- **Toast messages** for quick feedback
- **Badge counters** on navigation tabs
- **Activity indicators** in header
- **Modal alerts** for critical actions

### Notification Settings
- **Per-category controls** (documents, system, social)
- **Quiet hours** configuration
- **Delivery preferences** (push, email, both)
- **Frequency settings** (instant, digest, weekly)

---

## ⚡ Performance Considerations

### Loading States
- **Skeleton screens** during data loading
- **Progressive disclosure** of content
- **Lazy loading** for images and heavy content
- **Optimistic updates** for better perceived performance

### Offline Functionality
- **Cached dashboard data** for offline viewing
- **Local storage** of recent documents
- **Sync indicators** when reconnecting
- **Offline action queue** for later synchronization

### Memory Management
- **Virtual scrolling** for long lists
- **Image optimization** and lazy loading
- **Component cleanup** when navigating
- **Efficient state management** with Pinia 