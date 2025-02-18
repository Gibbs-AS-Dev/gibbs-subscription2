import { DateType } from './datetime';
export interface MbscRecurrenceRule {
    repeat?: 'daily' | 'weekly' | 'monthly' | 'yearly';
    interval?: number;
    count?: number;
    from?: DateType;
    until?: DateType;
    month?: number | number[];
    day?: number | number[];
    pos?: number;
    weekDays?: string;
    weekStart?: string;
}
