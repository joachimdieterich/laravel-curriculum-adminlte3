## 2025-08-15
- IndexWidget: added accessibility logic
- Groups: added scope for groups with default period_id
- changed modal-header structure
- changed dropdown-toggle animation
- deleted unused/deprecated ColorPicker-component
- fixed header and sidebar overlay problems
- fixed AchievementIndicators being visible in occasions where they shouldn't
- fixed badge-counter styling
- fixed objectives-styling for tablet/mobile view
- ObjectiveBox-Header: fixed arrow positioning and accessibility
- Objectives: wip on changing layout for mobile-view
- Plan/PlanAchievements: fixed buttons styling/structure and accessibility
- AchievementIndicator: show last-updated date in local date format
- LinkItem: fixed links overflowing and special-chars being parsed to HTML

## 2025-08-08
- Kanban: fixed 'collapse-all'-toggle not working
- KanbanItem: removed isAccessible-check on /editors-request (which was causing a 403 for guests)
- fixed media not always being accessible on KanbanItems
- EdusharingMediaAdapter: fixed redirect paramter
- RenderUsage: now sends /content-request for Edusharing-media on click
- RenderUsage: fixed overlay position
- fixed topnav dropdown-menu positioning

## 2025-08-06
- Home: admins now only get their subscribed logbooks instead of all
- added 'skip navigation'-button in Header for accessibility
- restructured scss-files
- Kanban: fixed accessibility for title-component
- Kanban: fixed not being able to remove description
- KanbanItem: fixed comments/reactions structure and added accessibility
- KanbanItem: fixed sending /editors-request if user is not given
- Map: fixed problem when no marker exists
- MapModal: fixed problem with type_id/category_id fields and added toast-notification
- MarkerModal: fixed some logic and form-field-types in controller
- added seeder for MapMarkerCategory/-Type

## 2025-07-23
- Kanban: fixed bug where authenticated users didn't have access through a token-link
- KanbanItem: fixed visible-from/-to logic
- Exams: fixed exams not loading
- Users: now ordering by lastname, then firstname
- TerminalObjective: fixed error on changing type order
- Token: reset title-field on create
- Curriculum: fixed reset_order_ids function

## 2025-07-16
- fixed tinyMCE-problems when setting up project
- CertificateController: copied missing function from old version
- KanbanModal: added toast-notifications