import { MbscCalendarSync, MbscCalendarSyncConfig } from './common';
export interface MbscGoogleCalendarSyncConfig extends MbscCalendarSyncConfig {
    apiKey?: string;
    auth?: 'client' | 'server';
    authUrl?: string;
    gapi?: any;
    gis?: any;
    refreshUrl?: string;
    scopes?: string;
}
export declare const googleCalendarSync: MbscCalendarSync<MbscGoogleCalendarSyncConfig>;
