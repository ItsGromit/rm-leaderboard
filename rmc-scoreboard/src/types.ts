// Define interfaces
export interface RecordDataRMC {
    submitTime: string;
    displayName: string;
    accountId: string;
    objective: 'author' | 'gold' | 'silver' | 'bronze';
    goals: number;
    belowGoals: number;
    skips?: number;
    videoLink?: string;
}

export interface RecordDataRMS {
    submitTime: string;
    displayName: string;
    accountId: string;
    objective: 'author' | 'gold' | 'silver' | 'bronze';
    goals: number;
    skips: number;
    timeSurvived: number;
    videoLink?: string;
}
