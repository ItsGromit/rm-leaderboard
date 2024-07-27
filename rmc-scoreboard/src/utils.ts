import type { RecordDataRMC, RecordDataRMS } from "./types";

export const formatTimeStamp = (timestamp: string) => {
    const date = new Date(timestamp);
    return date.toLocaleString('en-EN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

export const formatTimeSurvived = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours}h ${minutes}m ${secs}s`;
};

// Type guards
export function isRMC(item: any): item is RecordDataRMC {
    return (item as RecordDataRMC).belowGoals !== undefined;
}

export function isRMS(item: any): item is RecordDataRMS {
    return (item as RecordDataRMS).timeSurvived !== undefined;
}