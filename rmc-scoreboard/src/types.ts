// Define interfaces
export interface RecordDataRMC {
    submitTime: string;
    displayName: string;
    objective: 'author' | 'gold' | 'silver' | 'bronze';
    goals: number;
    belowGoals: number;
    skips?: number;
    verified: number;
}

export interface RecordDataRMS {
    submitTime: string;
    displayName: string;
    objective: 'author' | 'gold' | 'silver' | 'bronze';
    goals: number;
    skips: number;
    timeSurvived: number;
}
