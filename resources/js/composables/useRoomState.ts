import { ref } from 'vue';

export interface Room {
    id: number;
    name: string;
    capacity: string | number;
    color: string;
}

export interface Area {
    name: string;
    polygonColor: string;
    rooms: Room[];
}

export function useRoomState() {
    const selectedArea = ref<Area>({
        name: 'East Wing',
        polygonColor: '#3b82f6',
        rooms: [],
    });

    const hoveredRoomId = ref<number | null>(null);
    const hoveredRoomName = ref<string>('');

    const isEditMode = ref(false);
    const editingRoomId = ref<number | null>(null);

    const showRoomPrompt = ref(false);
    const isSaving = ref(false);

    const newRoom = ref({
        name: '',
        capacity: 0 as number | string,
        color: '#3b82f6',
    });

    const addRoom = (room: Room) => {
        selectedArea.value.rooms.push(room);
    };

    const updateRoomInList = (roomId: number, updates: Partial<Room>) => {
        const idx = selectedArea.value.rooms.findIndex((r) => r.id === roomId);
        if (idx !== -1) {
            selectedArea.value.rooms[idx] = {
                ...selectedArea.value.rooms[idx],
                ...updates,
            };
        }
    };

    const removeRoomFromList = (roomId: number) => {
        selectedArea.value.rooms = selectedArea.value.rooms.filter((r) => r.id !== roomId);
    };

    const setHoveredRoom = (roomId: number | null, roomName: string = '') => {
        hoveredRoomId.value = roomId;
        hoveredRoomName.value = roomName;
    };

    const setEditMode = (roomId: number | null, isEditing: boolean) => {
        editingRoomId.value = roomId;
        isEditMode.value = isEditing;
    };

    const setNewRoom = (name: string, capacity: number | string, color: string) => {
        newRoom.value = { name, capacity, color };
    };

    const resetNewRoom = () => {
        newRoom.value = { name: '', capacity: 0, color: '#3b82f6' };
    };

    return {
        selectedArea,
        hoveredRoomId,
        hoveredRoomName,
        isEditMode,
        editingRoomId,
        showRoomPrompt,
        isSaving,
        newRoom,
        addRoom,
        updateRoomInList,
        removeRoomFromList,
        setHoveredRoom,
        setEditMode,
        setNewRoom,
        resetNewRoom,
    };
}
