# Permissions Management Module

## Overview
The Permissions Management module allows administrators to manage system permissions and user access levels based on the `admin_tabs` database table.

## Features

### 1. View All Permissions
- Display all system permissions in a sortable, searchable table
- Color-coded badges for different permission types and levels
- Quick status toggle functionality

### 2. Add New Permissions
- Create new permissions with all necessary fields
- Validation for required fields
- Support for both page and function permissions

### 3. Edit Existing Permissions
- Modify any existing permission settings
- Update access levels, grouping, positioning
- Manage special user assignments

### 4. Permission Fields Explanation

| Field | Description | Required | Example |
|-------|-------------|----------|---------|
| **Keyword** | Unique identifier for the permission | Yes | `tab_dashboard` or `manage_users` |
| **Name** | Display name shown in menus | Yes | `Dashboard` or `Manage Users` |
| **Link** | URL route/path (empty for functions) | No | `dashboard` or `admin/users` |
| **Grouping** | Menu group number (0 = ungrouped) | Yes | `1` (main menu) |
| **Level** | Required access level | Yes | `admin`, `all`, or specific role |
| **Type** | 1 = Page, 2 = Function | Yes | `1` (Page) or `2` (Function) |
| **Position** | Display order within group | Yes | `1`, `2`, `3` etc. |
| **Icon** | FontAwesome icon class | No | `fas fa-dashboard` |
| **Special User** | Specific employee IDs with access | No | `emp001,emp002` |
| **Exclude User** | Employee IDs to exclude | No | `emp003,emp004` |
| **Status** | 1 = Active, 0 = Inactive | System | `1` (Active) |

### 5. Access Levels

- **All Users**: Available to all logged-in users
- **Admin Only**: Restricted to admin level users
- **Specific Roles**: Based on user levels defined in `admin_lang` table

### 6. Permission Types

#### Page Permissions (Type = 1)
- Represent actual pages/modules in the system
- Have a link/route associated
- Appear in navigation menus
- Example: Dashboard, Employee Management, Leaves

#### Function Permissions (Type = 2)
- Represent specific actions/functions
- No associated link (used for access control)
- Control granular permissions within pages
- Example: Add Employee, Delete Leave, Approve Timesheet

## Usage Instructions

### Adding a New Permission

1. Click the "Add Permission" button
2. Fill in the required fields:
   - **Keyword**: Create a unique identifier (e.g., `tab_reports`)
   - **Name**: Enter the display name (e.g., `Reports`)
   - **Link**: Add the URL route if it's a page (e.g., `reports`)
   - **Grouping**: Set the menu group (usually `1` for main menu)
   - **Level**: Choose the access level (`admin`, `all`, or specific role)
   - **Type**: Select `1` for Page or `2` for Function
   - **Position**: Set the display order
3. Optionally add icon, special users, etc.
4. Click "Save Permission"

### Editing a Permission

1. Click the edit button (pencil icon) on any row
2. Modify the fields as needed
3. Click "Save Permission"

### Managing Status

- Click the status button to toggle between Active/Inactive
- Inactive permissions are not enforced by the system

### Deleting a Permission

1. Click the delete button (trash icon)
2. Confirm the deletion in the popup
3. **Warning**: This action cannot be undone

## Integration with System

The permissions module integrates with the existing CodeIgniter permission system through:

1. **check_function()** helper - Checks function permissions
2. **Navigation system** - Controls menu item visibility
3. **Access control** - Restricts page/module access

## Database Structure

The module uses the `admin_tabs` table with the following structure:

```sql
CREATE TABLE `admin_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `grouping` int(11) NOT NULL,
  `level` text NOT NULL,
  `special_user` text NOT NULL,
  `exclude_user` text NOT NULL,
  `icon` varchar(150) NOT NULL,
  `position` int(5) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1_page, 2_function',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1-active',
  PRIMARY KEY (`id`)
);
```

## Security Notes

- Only users with admin access can manage permissions
- All actions are logged through the system's audit trail
- Changes take effect immediately upon saving
- Always test permission changes in a development environment first

## Troubleshooting

### Permission Not Working
1. Check if permission status is Active
2. Verify the keyword matches what's used in code
3. Ensure user has the required access level
4. Check for conflicting exclude_user settings

### Menu Item Not Showing
1. Verify Type is set to 1 (Page)
2. Check the Link field is not empty
3. Ensure Grouping and Position are set correctly
4. Confirm user has required access level