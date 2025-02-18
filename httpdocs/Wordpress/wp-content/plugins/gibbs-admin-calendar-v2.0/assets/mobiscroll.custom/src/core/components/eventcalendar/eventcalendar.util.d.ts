import { MbscCalendarEvent, MbscCalendarEventData, MbscResource } from '../../shared/calendar-view/calendar-view.types';
import { DateType, IDatetimeProps } from '../../util/datetime';
import { MbscEventcalendarOptions } from './eventcalendar.types';
/** @hidden */
export declare function getDataInRange(data: any[], s: MbscEventcalendarOptions, firstDay: Date, lastDay: Date, start?: DateType, end?: DateType): MbscCalendarEvent[];
/** @hidden */
export declare function getEventId(): string;
/** @hidden */
export declare function getEventData(s: IDatetimeProps, event: MbscCalendarEvent, eventDay: Date, colorEvent: boolean, resource?: MbscResource, isList?: boolean, isMultipart?: boolean, isDailyResolution?: boolean, skipLabels?: boolean): MbscCalendarEventData;
/** @hidden */
export declare function prepareEvents(events?: MbscCalendarEvent[]): MbscCalendarEvent[];
/** @hidden */
export declare function checkInvalidCollision(s: any, invalids: {
    [key: string]: MbscCalendarEvent[];
} | undefined, valids: {
    [key: string]: MbscCalendarEvent[];
} | undefined, start: Date, end: Date, min: number, max: number, invalidateEvent: 'start-end' | 'strict' | undefined, exclusiveEndDates?: boolean): MbscCalendarEventData | boolean;
