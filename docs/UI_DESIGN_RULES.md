# FDMS UI Design Rules

This document defines the design rules, patterns, and standards for creating wireframes for the Fast Document Management System (FDMS). Follow these guidelines to ensure consistency across all wireframe designs.

## Project Context

- **Target Audience**: Real estate agents
- **Language**: Vietnamese
- **Platform**: Mobile-first design
- **Domain**: Real estate document management and survey forms

## Technical Specifications

### Canvas & Frame
- **Dimensions**: 390x844 (mobile screen size)
- **Frame**: 
  - Background: `#f8f9fa`
  - Border: `#000`, 2px width
  - Rounded corners: `rx="25"`

### Status Bar
- **Position**: Top of frame (0, 0)
- **Dimensions**: 390x44
- **Style**: 
  - Background: `#000`
  - Rounded corners: `rx="25"`
  - Time display: "9:41 AM" (white, 14px, Arial, centered)

## Layout Structure

### Header Bar
- **Position**: Below status bar (y=44)
- **Dimensions**: 380x60 (inset by 5px from frame edges)
- **Style**:
  - Background: `white`
  - Border: `#ddd`, 1px
  - Rounded corners: `rx="5"`
  
**Header Elements:**
- **Left**: Back button (blue caret `‹`, 20px, #0066CC, x=25)
- **Center**: Blueprint/Screen title (16px bold, #333, centered at x=195)
- **Right**: User avatar (circle, 16px radius, #0066CC background, x=365)

### Main Content Area
- **Position**: Below header bar (y=104)
- **Background**: White
- **Padding**: 20px from frame edges

### Bottom Bar
- **Position**: 59px from bottom (y=770)
- **Dimensions**: 380x59 (inset by 5px)
- **Style**: Same as header bar
- **Buttons**: Two action buttons (Save/Submit pattern)

## Design Components

### Document Title Area
- **Container**: White background, #ddd border, 8px rounded corners
- **Dimensions**: 350x80
- **Elements**:
  - Document title (14px bold, #333)
  - Status badge (right-aligned, rounded badge with status text)
  - Sections list button (if applicable)

### Form Fields

#### Text Input Fields
- **Container**: White background, #ccc border, 5px rounded corners
- **Height**: 35-40px
- **Label**: Above field, 11-12px, #666 color
- **Placeholder**: #999 color, italic style suggested
- **Input text**: #333 color, 14px

#### Dropdown Fields
- **Style**: Same as text inputs
- **Indicator**: Down arrow (▼) on right side, #666 color
- **Selected value**: #333 color, 14px

#### Map Component
- **Container**: Light blue background (#e8f4fd), #ccc border
- **Inner map**: #d1ecf1 background, #bee5eb border
- **Features**: Gray road lines of varying weights (0.5px-2px)
- **Marker**: Red circle (#dc3545), 4px radius
- **Coordinates**: Below map, 12px, #333

### Buttons

#### Primary Actions (Submit)
- **Background**: #0066CC (blue)
- **Text**: White, 16px bold
- **Dimensions**: 140x40
- **Rounded corners**: 5px

#### Secondary Actions (Save)
- **Background**: #6c757d (gray)
- **Text**: White, 16px bold
- **Dimensions**: 140x40
- **Rounded corners**: 5px

#### Navigation Elements
- **Sections List Button**: 
  - Circular/rounded background (#f0f0f0)
  - Border: #ccc
  - Icon: Hamburger menu (☰)
  - Size: 30x30

### Status Badges
- **Draft (Nháp)**: #ffc107 background, white text
- **Submitted**: #28a745 background, white text
- **Approved**: #28a745 background, white text
- **Rejected**: #dc3545 background, white text
- **Style**: Rounded corners (12px), 12px text, centered

## Typography

### Font Family
- Primary: Arial (web-safe fallback)

### Font Sizes & Weights
- **Headers**: 16px bold
- **Body text**: 14px regular
- **Labels**: 11-12px regular
- **Small text**: 10px regular
- **Buttons**: 16px bold
- **Navigation**: 20px (back button)

### Colors
- **Primary text**: #333
- **Secondary text**: #666
- **Placeholder text**: #999
- **Links/Actions**: #0066CC
- **Borders**: #ccc, #ddd
- **Backgrounds**: white, #f8f9fa, #f0f0f0

## Spacing & Layout

### Margins & Padding
- **Screen edges**: 20-30px
- **Between sections**: 15-20px
- **Between form fields**: 10-15px
- **Internal component padding**: 10px

### Element Positioning
- **Left alignment**: x=25-30 for most elements
- **Center alignment**: x=195 (half of 390px)
- **Right alignment**: x=350-365
- **Field width**: 330px (with 30px margins)

## Content Guidelines

### Vietnamese Text Examples
- **Login**: "Đăng nhập"
- **Save**: "Lưu"
- **Submit**: "Gửi"
- **Draft**: "Nháp"
- **Approved**: "Đã duyệt"
- **Rejected**: "Từ chối"

### Real Estate Terminology
- **Property types**: "Đất trống", "Nhà ở", "Chung cư"
- **Address fields**: "Tỉnh/Thành phố", "Huyện/Quận", "Xã/Phường"
- **Survey terms**: "Khảo sát hiện trạng nhà đất"

### Text Clipping Rules
- **Long titles**: Truncate with ellipsis (...) when text exceeds container width
- **Document titles**: Clip at approximately 50-60 characters in lists and cards
- **Address text**: Prioritize essential information, clip administrative details
- **General rule**: Show enough text to identify content, use ellipsis for overflow
- **Interactive elements**: Ensure clipped text can be revealed through interaction (tap/hover)

## Interaction Patterns

### Navigation
- Back button: Blue left caret (‹) without circle
- Section navigation: Hamburger menu button
- User actions: Avatar tap → drawer menu

### Form Interaction
- Dropdown indicators with down arrows
- Clear visual hierarchy with labels above inputs
- Grouped related fields (address components)

### Action Buttons
- Primary action (Submit): Blue, right-positioned
- Secondary action (Save): Gray, left-positioned
- Full-width mobile-friendly button sizing

## File Organization

### Naming Convention
- Screen wireframes: `screen-[number]/[section-name].svg`
- Example: `screen-1/address-section.svg`

### SVG Structure
```xml
<!-- Phone Frame -->
<!-- Status Bar -->
<!-- Header Bar -->
<!-- Main Content -->
<!-- Bottom Bar -->
```

## Quality Standards

### Visual Polish
- Respect frame rounded corners (no overlapping bars)
- Consistent spacing and alignment
- Clear visual hierarchy
- Appropriate contrast ratios

### Usability
- Touch-friendly element sizes (minimum 44px height)
- Clear interactive elements
- Logical information flow
- Mobile-optimized layouts

### Content Accuracy
- Use actual Vietnamese content where specified
- Realistic placeholder data
- Proper address hierarchy (Province → District → Ward)
- Domain-appropriate terminology

## Usage Instructions

When creating new wireframes:

1. **Start with frame**: Copy base structure from existing wireframe
2. **Follow spacing**: Use established margins and padding
3. **Use color palette**: Stick to defined color scheme
4. **Check typography**: Apply correct font sizes and weights
5. **Validate content**: Ensure Vietnamese text is accurate
6. **Test layout**: Verify elements don't overlap frame borders
7. **Add interactions**: Include appropriate buttons and navigation

This document should be referenced for all FDMS wireframe creation to maintain design consistency and quality standards. 