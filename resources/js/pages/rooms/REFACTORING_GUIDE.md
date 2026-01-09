# Room.vue Refactoring - OOP Architecture Guide

## Overview
The Room.vue component has been refactored following **Object-Oriented Programming (OOP)** principles and **Separation of Concerns (SoC)** to improve code maintainability, testability, and reusability.

## Architecture Structure

```
Room Component
├── useRoomState (Composable)
│   └── State Management
├── RoomApiService (Service Class)
│   ├── API Communication
│   └── RoomDataMapper (Mapper)
├── RoomManager (Orchestrator Class)
│   └── Business Logic
├── PolygonManager (Map Handler)
│   └── Google Maps Polygon Operations
└── MapInitializer (Setup Helper)
    └── Map & Drawing Manager Configuration
```

## Components & Their Responsibilities

### 1. **useRoomState.ts** - State Management (Composable)
**Location:** `resources/js/composables/useRoomState.ts`

**Responsibility:** Centralized reactive state management for the room interface.

**Key Methods:**
- `addRoom()` - Add a new room to the list
- `updateRoomInList()` - Update an existing room's properties
- `removeRoomFromList()` - Remove a room from the list
- `setHoveredRoom()` - Update hover state
- `setEditMode()` - Toggle edit mode
- `setNewRoom()` - Set new room form data
- `resetNewRoom()` - Clear new room form

**Benefits:**
- Single source of truth for component state
- Easy to test state transitions
- Reusable across multiple components if needed

---

### 2. **RoomApiService.ts** - API Communication (Service Class)
**Location:** `resources/js/services/RoomApiService.ts`

**Responsibility:** Handle all HTTP requests to the backend API.

**Classes:**

#### RoomApiService
Methods:
- `fetchRooms()` - GET all rooms
- `createRoom(payload)` - POST create new room
- `updateRoom(roomId, payload)` - PUT update room
- `deleteRoom(roomId)` - DELETE room

#### RoomDataMapper
Methods:
- `mapFromApi(apiRoom)` - Convert API response to frontend Room object
- `mapToPayload(room)` - Convert frontend Room to API payload format

**Benefits:**
- Isolated API logic - easy to switch API endpoints
- Centralized error handling
- Automatic data transformation (API ↔ Frontend)
- Testable without mocking HTTP calls

---

### 3. **PolygonManager.ts** - Map Polygon Handler (Service Class)
**Location:** `resources/js/services/PolygonManager.ts`

**Responsibility:** Manage all Google Maps polygon operations.

**Key Methods:**
- `enableDrawMode()` - Activate polygon drawing
- `disableDrawMode()` - Deactivate drawing (enable pan/zoom)
- `setActivePolygon()` - Set the currently drawn polygon
- `getActivePolygonPath()` - Extract polygon coordinates
- `drawRoomPolygon()` - Render a room polygon on the map
- `updatePolygonColor()` - Change polygon color dynamically
- `removePolygon()` - Delete a polygon from the map
- `clearActivePolygon()` - Clear the current drawing

**Features:**
- Handles all polygon event listeners (hover, click)
- Manages polygon styling
- Color management (random color fallback)

**Benefits:**
- Decoupled from React component logic
- Easy to extend polygon features
- Reusable for other map-based features

---

### 4. **RoomManager.ts** - Business Logic Orchestrator (Service Class)
**Location:** `resources/js/services/RoomManager.ts`

**Responsibility:** Orchestrate room operations by combining API service and polygon manager.

**Key Methods:**
- `loadAndDrawRooms()` - Load rooms from API and render on map
- `createRoom()` - Create room with polygon
- `updateRoom()` - Update room and sync polygon color
- `deleteRoom()` - Delete room and remove polygon
- `validateRoomData()` - Validate room data before submission

**Benefits:**
- Single point of control for room operations
- Coordinates between API and Map layers
- Encapsulates validation logic
- Easy to add complex business rules

---

### 5. **MapInitializer.ts** - Map Setup Helper (Service Class)
**Location:** `resources/js/services/MapInitializer.ts`

**Responsibility:** Initialize Google Maps, Drawing Manager, and Polygon Manager.

**Key Methods:**
- `initialize()` - Set up map and managers
- `getMap()` - Retrieve initialized map instance
- `getPolygonManager()` - Retrieve polygon manager instance

**Benefits:**
- Separates initialization logic from component
- Reusable map setup configuration
- Cleaner component lifecycle

---

## Separation of Concerns

| Concern | Handler | Purpose |
|---------|---------|---------|
| **UI State** | useRoomState | Manage reactive data |
| **API Communication** | RoomApiService | Network requests & data mapping |
| **Map Drawing** | PolygonManager | Google Maps operations |
| **Business Logic** | RoomManager | Coordinate API + Map operations |
| **Map Setup** | MapInitializer | Configuration & initialization |
| **UI/Templates** | Room.vue | Render and listen to events |

---

## Data Flow

```
1. Component mounts
   ↓
2. MapInitializer.initialize() → Creates map, drawing manager, polygon manager
   ↓
3. RoomManager.loadAndDrawRooms() → Fetches rooms via RoomApiService
   ↓
4. RoomDataMapper transforms API data → Frontend format
   ↓
5. PolygonManager draws each room polygon on map
   ↓
6. useRoomState stores rooms in reactive state
   ↓
7. Vue template renders rooms from state
```

## Usage Example in Component

```typescript
// Initialize services
const roomManager = new RoomManager(polygonManager);

// Load rooms from API
await roomManager.loadAndDrawRooms(
    (rooms) => { selectedArea.value.rooms = rooms; },
    (room) => { polygonManager.drawRoomPolygon(room, ...) }
);

// Create new room
const result = await roomManager.createRoom(
    { name: 'Room A', capacity: 30, color: '#3b82f6' },
    polygonPath
);

// Update room
await roomManager.updateRoom(roomId, {
    name: 'Updated Room',
    capacity: 40,
    color: '#8b5cf6'
});

// Delete room
await roomManager.deleteRoom(roomId);
```

## OOP Principles Applied

✅ **Single Responsibility Principle** - Each class has one reason to change
✅ **Open/Closed Principle** - Open for extension, closed for modification
✅ **Dependency Injection** - PolygonManager injected into RoomManager
✅ **Encapsulation** - Private properties/methods, public interfaces
✅ **Composition** - RoomManager uses services, not inheritance
✅ **DRY (Don't Repeat Yourself)** - RoomDataMapper eliminates duplicate mapping logic

## Benefits of This Architecture

| Benefit | Impact |
|---------|--------|
| **Testability** | Each service can be unit tested independently |
| **Reusability** | Services can be used in other components |
| **Maintainability** | Changes isolated to specific files |
| **Scalability** | Easy to add new features without touching core logic |
| **Readability** | Clear separation makes code self-documenting |
| **Debugging** | Issues can be traced to specific layers |

## Extension Points

### Adding a new feature (e.g., export room map):
```typescript
// In RoomManager
async exportRoomMap(roomId: number) {
    const room = // fetch room details
    const polygonData = polygonManager.getPolygonData(roomId);
    // Generate export...
}
```

### Changing API endpoint:
```typescript
// Only modify RoomApiService
async createRoom(payload) {
    const res = await axios.post('/api/v2/rooms', payload); // Changed endpoint
}
```

### Adding polygon animation:
```typescript
// Only modify PolygonManager
animatePolygon(roomId: number) {
    const poly = this.roomPolygons[roomId];
    // Add animation logic
}
```

---

## Migration Notes

The refactoring maintains all existing functionality while improving code organization. No API or feature changes were made.

---

**Author:** AI Assistant  
**Date:** January 9, 2026  
**Version:** 1.0
