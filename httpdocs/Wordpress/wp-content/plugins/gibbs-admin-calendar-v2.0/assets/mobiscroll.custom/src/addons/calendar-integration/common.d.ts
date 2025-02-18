import { MbscCalendarEvent } from '../../core/shared/calendar-view/calendar-view.types';
import { ITimezonePlugin } from '../../core/util/datetime';
export declare const win: any;
export interface MbscCalendarSyncConfig {
    clientId?: string;
    timezone?: string;
    timezonePlugin?: ITimezonePlugin;
    onInit?(): void;
    onSignedIn?(): void;
    onSignedOut?(): void;
}
export interface MbscCalendarSync<T extends MbscCalendarSyncConfig> {
    init(config: T): void;
    signIn(): Promise<any>;
    signOut(): Promise<any>;
    isSignedIn(): boolean;
    getCalendars(callback?: (calendars: any[]) => void): Promise<any>;
    getEvents(calendarIds: string | string[], start: Date, end: Date, callback?: (events: MbscCalendarEvent[]) => void): Promise<any>;
    addEvent(calendarId: string, event: MbscCalendarEvent, callback?: (addedEvent: MbscCalendarEvent) => void): Promise<any>;
    updateEvent(calendarId: string, event: MbscCalendarEvent, callback?: (updatedEvent: MbscCalendarEvent) => void): Promise<any>;
    deleteEvent(calendarId: string, event: MbscCalendarEvent, callback?: (deletedEvent: MbscCalendarEvent) => void): Promise<any>;
}
export declare function returnValue(callback: any, value: any): any;
export declare function loadScript(url: string, callback?: any): void;
