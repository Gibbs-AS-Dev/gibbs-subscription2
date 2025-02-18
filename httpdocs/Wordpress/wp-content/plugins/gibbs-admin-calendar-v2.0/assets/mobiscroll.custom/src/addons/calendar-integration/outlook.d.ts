import { MbscCalendarSync, MbscCalendarSyncConfig } from './common';
export interface MbscOutlookCalendarSyncConfig extends MbscCalendarSyncConfig {
    msal?: any;
    msalClient?: any;
    pageSize?: number;
    redirectUri: string;
}
export declare const outlookCalendarSync: MbscCalendarSync<MbscOutlookCalendarSyncConfig>;
