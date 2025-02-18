import { IDatetimeProps } from '../../util/datetime';
import { ICalendarLabelData, ICalendarProps, ICalendarViewProps, MbscCalendarLabel } from './calendar-view.types';
export declare const MONTH_VIEW = "month";
export declare const YEAR_VIEW = "year";
export declare const MULTI_YEAR_VIEW = "multi-year";
export declare const PAGE_WIDTH = 296;
export declare const calendarViewDefaults: ICalendarProps;
/**
 * @hidden
 * Returns the first date of the given page.
 * The pages are defined by the eventRange and eventRangeSize props.
 */
export declare function getFirstPageDay(pageIndex: number, s: ICalendarViewProps): Date;
/** @hidden */
export declare function getPageIndex(d: Date, s: ICalendarViewProps): number | undefined;
/** @hidden */
export declare function getYearsIndex(d: Date, s: ICalendarViewProps): number;
/** @hidden */
export declare function getYearIndex(d: Date, s: ICalendarViewProps): number;
/** @hidden */
export declare function getPageNr(pages: number | 'auto' | undefined, width: number | undefined): number;
/** @hidden */
export declare function getLabels(s: IDatetimeProps, labelsObj: {
    [key: string]: MbscCalendarLabel[];
}, start: Date, end: Date, maxLabels: number, days: number, allDayOnly: boolean, firstWeekDay: number, isMultiRow: boolean, eventOrder?: (a: MbscCalendarLabel, b: MbscCalendarLabel) => number, noOuterDays?: boolean, showLabelCount?: boolean, moreEventsText?: string, moreEventsPluralText?: string): {
    [key: string]: ICalendarLabelData;
};
/** @hidden */
export declare function sortEvents(events: MbscCalendarLabel[], eventOrder?: (a: MbscCalendarLabel, b: MbscCalendarLabel) => number): MbscCalendarLabel[];
