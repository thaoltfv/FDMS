# Essential UI Elements

## Menu: Main

**Items:**

- Home (feed)
- Documents (main feature that access blueprints and documents)
- Help (FAQ)
- Privacy policy
- Terms of service
- About

## Menu: Document sections list

* Present when editing/viewing a document.
* List of sections of the current document (if has permissions to view).

## Menu: Blueprints list (not a document list)

* Present when user already select a blueprint and is viewing a list of documents
belonging to the selected blueprint.
* Act as a global independent filter for the document list.
* Full visible on tablet-sized screens or larger.
* Collapsed into a button (i.e. "(Blueprints ⌄)") on mobile-sized screens.
* Items: List of blueprints (if has permissions to view).

## Menu: User actions

**Behavior**

* Open when user clicks on the avatar (in the top right corner).
* Appears as a Drawer.

**Top anchor**

```
┌─────────────────────────────────────────┐
│               User Full name    [AVA]   │
│                    User role    [TAR]   │
└─────────────────────────────────────────┘
```

**Menu items:**

- Notifications (with number badge)
- Profile
- Preferences

**Bottom anchor**

- Logout

# Mobile Phone UI Design

## Overall layout

### Header

**Left**

* Screens:
    - Back button
* Modals:
    - Modal title

**Center**

- Screen title
- Modal: not present

**Right**

* Screens:
    - Avatar (trigger user actions menu) with number badge for notifications.
    Appeareance: circle
* Modals:
    - Modal closing action buttons (i.g. "OK", "Cancel", "Close")

### Footer

**For screens**

No footer.

**For modals**

Usually not present. If present, it appear as sticky bottom pane and renders the
modal actions buttons (i.e. "Deny", "Approve").

### Main content

Renders the main content of the screen.

### Modals

Renders the modals of the screen.

# Demo content

UI text language: Vietnamese.
Target audience: real estate agents.
Screen-size: 390x844.

Required screens:

- Login
- Home feed
- Documents list
- Document editor

## Screen 0.1: Login

**Title:**

"Đăng nhập"

**Fields:**

- Email
- Mật mã

**Buttons:**

- Đăng nhập
- Quên mật khẩu
- Đăng ký

## Screen 0.2: Home feed

**Title:**

"Trang chủ"

**Content:**

- List of interested documents (i.e. documents that user has permissions to view)
    - QSDĐ và CTXD tại Phường Thới Hòa, thành phố Bến Cát, tỉnh Bình Dương
    - QSDĐ tại đường ĐT 755, thôn 8, xã Thống Nhất, huyện Bù Đăng, tỉnh Bình Phước
- List of blueprints (title: "Tập tài liệu")
    - Khảo sát hiện trạng nhà đất

## Screen 0.3: Documents list

**Title:**

"Khảo sát hiện trạng nhà đất"

**Content:**

- List of documents (i.e. documents that user has permissions to view)
    - QSDĐ tại thôn 3, xã Đăk Ơ, huyện Bù Gia Mập, tỉnh Bình Phước
        - Hoàng Anh Tuấn
        - Nháp
    - QSDĐ Tại phước Bình Phước Long ,bình Phước
        - Nguyễn Khoa
        - Nháp
    - QSDĐ tại thôn 4, xã Đường 10, huyện Bù Đăng, tỉnh Bình Phước
        - Hoàng Anh Tuấn
        - Đã gửi
    - QSDĐ tại đường Thống Nhất, phước Bình, Phước Long, Bình Phước
        - Lê Thị Thu
        - Đã gửi
    - QSDĐ tại đường ĐT 755, thôn 8, xã Thống Nhất, huyện Bù Đăng, tỉnh Bình Phước
        - Lê Thị Thu
        - Đã gửi
    - QSDĐ tại đường Độc Lập, phường Phước Bình, TX Phước Long, tỉnh Bình Phước
        - Lê Thị Thu
        - Đã duyệt
    - QSDĐ tại thôn 8, xã Long Bình, huyện Phú Riềng, tỉnh Bình Phước
        - Lê Thị Niềm
        - Từ chối
    - QSDĐ tại thôn 4, xã BomBo, huyện Bù Đăng, tỉnh Bình Phước
        - Hoàng Anh Tuấn
        - Đã gửi
    - QSDĐ tại Phường Chánh Mỹ, thành phố Thủ Dầu Một, tỉnh Bình Dương
        - Lê Thị Thu
        - Đã gửi
    - QSDĐ tại Bù Dinh, xã Thanh An, huyện Hớn Quản, tỉnh Bình Phước
        - Lê Thị Thu
        - Đã gửi
    - QSDĐ tại khu Đức Lập, thị trấn Đức Phong, tỉnh Bình Phước
        - Hoàng Anh Tuấn
        - Đã duyệt
    - QSDĐ tại thôn Bình Hà 1, xã Đakia , huyện Bù Gia Mập, tỉnh Bình Phước
        - Nguyễn Khoa
        - Đã duyệt
    - QSDĐ tại thôn Tân Lực, xã Bù Nho, huyện Phú Riềng, tỉnh Bình Phước
        - Lê Thị Thu
        - Từ chối

## Screen 1.0: Document editor (no required, substitute by screen-1.1)

- Layout: default

- Header bar: Back button.
- Document title: should be clipped if too long.
- Sections list button: half-rounded button on stick on the left screen. Vertically
aligned with the the document title, icon: bullets list.
- Document status: a small badge on the right side of the document title, icon:
  checkmark(muted or green) or cross.
- Active section: Text on the left.
- Form: renders form fields of the active section.
- Bottom bar: sticky bottom pane, 2 buttons: "Save" and "Submit".

### Shared content

**Document title**: "QSDĐ tại thôn 3, xã Đăk Ơ, huyện Bù Gia Mập, tỉnh Bình Phước"

**Document status**: "Nháp"

**List of sections:**

- Thông tin địa chỉ
- Khả năng tiếp cận
- Hình ảnh hiện trạng
- Quyền sử dụng đất
- Công trình xây dựng
- Pháp lý tài sản
- Xét duyệt

### Screen-1.1: Address section

Active section: "Thông tin địa chỉ"

List of Form fields:

- Loại tài sản: a single selection of types of assets (selected "Đất trống")
- "Tọa độ": a small embeded map with a marker (selected "11.521841,106.81104")
- "Địa chỉ": A multistep selection of administrative units
    - Tỉnh/Thành phố (selected "Bình Phước")
    - Huyện/Quận (selected "Bù Gia Mập")
    - Xã/Phường (selected "Đăk Ơ")
- Tên đường
- Số nhà
- Số tờ
- Số thửa

### Screen-1.2: Display sections list

Active section: "Thông tin địa chỉ"

Show a drawer with the list of sections.
Background is content of screen-1.1.