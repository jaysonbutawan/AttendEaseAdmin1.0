# Room Component Refactoring - File Structure

```
resources/js/
├── pages/
│   └── rooms/
│       ├── Room.vue (REFACTORED)
│       │   ├── Imports composables & services
│       │   ├── Uses useRoomState for state management
│       │   ├── Uses RoomManager for business logic
│       │   └── Uses PolygonManager for map operations
│       │
│       └── REFACTORING_GUIDE.md (Documentation)
│
├── composables/
│   └── useRoomState.ts (NEW)
│       ├── Room & Area interfaces
│       └── Reactive state management functions
│
└── services/
    ├── RoomApiService.ts (NEW)
    │   ├── RoomApiService class
    │   │   ├── fetchRooms()
    │   │   ├── createRoom()
    │   │   ├── updateRoom()
    │   │   └── deleteRoom()
    │   │
    │   └── RoomDataMapper class
    │       ├── mapFromApi()
    │       └── mapToPayload()
    │
    ├── PolygonManager.ts (NEW)
    │   ├── enableDrawMode()
    │   ├── disableDrawMode()
    │   ├── drawRoomPolygon()
    │   ├── updatePolygonColor()
    │   ├── removePolygon()
    │   └── Event handling
    │
    ├── RoomManager.ts (NEW)
    │   ├── loadAndDrawRooms()
    │   ├── createRoom()
    │   ├── updateRoom()
    │   ├── deleteRoom()
    │   └── validateRoomData()
    │
    └── MapInitializer.ts (NEW)
        ├── initialize()
        ├── getMap()
        └── getPolygonManager()
```

## Class Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     Room.vue Component                      │
│                   (User Interface Layer)                    │
└────────────┬────────────────────────────────────┬───────────┘
             │                                    │
             ▼                                    ▼
    ┌──────────────────┐           ┌──────────────────────────┐
    │ useRoomState     │           │   MapInitializer         │
    │  (State Mgmt)    │           │   (Setup Helper)         │
    │                  │           │                          │
    │ • selectedArea   │           │ • initialize()           │
    │ • hoveredRoomId  │           │ • getMap()               │
    │ • isEditMode     │           │ • getPolygonManager()    │
    │ • newRoom        │           └──────────────┬───────────┘
    └──────────────────┘                         │
                                                 ▼
                                    ┌─────────────────────────┐
                                    │ PolygonManager          │
                                    │ (Map Operations)        │
                                    │                         │
                                    │ • enableDrawMode()      │
                                    │ • drawRoomPolygon()     │
                                    │ • updatePolygonColor()  │
                                    │ • removePolygon()       │
                                    └─────────────────────────┘
                                                 ▲
                                                 │
                                    ┌────────────┴────────────┐
                                    │                         │
                        ┌───────────▼──────────┐   ┌──────────▼──────┐
                        │ RoomApiService       │   │ RoomManager     │
                        │ (API Communication)  │   │ (Orchestrator)  │
                        │                      │   │                 │
                        │ • fetchRooms()       │   │ • createRoom()  │
                        │ • createRoom()       │   │ • updateRoom()  │
                        │ • updateRoom()       │   │ • deleteRoom()  │
                        │ • deleteRoom()       │   │ • validate()    │
                        │                      │   │                 │
                        └───────────┬──────────┘   └─────────────────┘
                                    │
                                    ▼
                        ┌──────────────────────┐
                        │ RoomDataMapper       │
                        │ (Data Transform)     │
                        │                      │
                        │ • mapFromApi()       │
                        │ • mapToPayload()     │
                        └──────────────────────┘
```

## Interaction Flow

```
User Action (UI)
       │
       ▼
Room.vue Event Handler
       │
       ├──► useRoomState (update UI state)
       │
       ├──► RoomManager (business logic)
       │    │
       │    ├──► RoomApiService (HTTP request)
       │    │    │
       │    │    └──► RoomDataMapper (transform data)
       │    │
       │    └──► PolygonManager (update map)
       │         │
       │         └──► Google Maps API
       │
       └──► Update UI with results
```

## State Flow

```
API Response
    │
    ▼
RoomDataMapper.mapFromApi()
    │
    ▼
Frontend Room Object
    │
    ▼
RoomManager (coordinates operations)
    │
    ├──► useRoomState (store in reactive state)
    │
    └──► PolygonManager (visualize on map)
    
User sees updated UI + Map visualization
```

## Service Dependencies

```
Room.vue
├── depends on → useRoomState
├── depends on → MapInitializer
├── depends on → RoomManager
│                ├── depends on → RoomApiService
│                │                └── depends on → RoomDataMapper
│                └── depends on → PolygonManager
└── depends on → PolygonManager
```

## Creating/Updating Room Flow

```
1. User fills form & clicks save
   │
   ▼
2. Room.vue calls roomManager.createRoom()
   │
   ▼
3. RoomManager.createRoom()
   │
   ├─→ RoomApiService.createRoom()
   │   │
   │   └─→ API POST /room_polygon
   │       │
   │       ├─→ RoomDataMapper.mapFromApi()
   │       │
   │       └─→ Returns Room object
   │
   ├─→ RoomManager adds room to state
   │
   └─→ PolygonManager draws polygon on map
       │
       └─→ Google Maps polygon rendered
           
4. UI updates automatically (Vue reactivity)
5. User sees new room in sidebar + on map
```
