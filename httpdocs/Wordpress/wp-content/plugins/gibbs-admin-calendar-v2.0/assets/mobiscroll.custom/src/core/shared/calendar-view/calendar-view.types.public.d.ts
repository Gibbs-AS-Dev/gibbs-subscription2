import { ICalendarData } from './calendar-view.types';
export interface MbscResource {
    id: number | string;
    name?: string;
    collapsed?: boolean;
    color?: string;
    depth?: number;
    isParent?: boolean;
    children?: MbscResource[];
    eventCreation?: boolean;
    [x: string]: any;
}
export interface MbscCalendarMarked extends ICalendarData {
    /** Color of the mark. */
    color?: string;
    /** CSS class for the mark. */
    markCssClass?: string;
    /** CSS class for the cell. */
    cellCssClass?: string;
}
export interface MbscCalendarColor extends ICalendarData {
    /** Background of the circle. */
    highlight?: string;
    /** Background of the cell. */
    background?: string;
    /** CSS class for the cell. */
    cellCssClass?: string;
}
export interface MbscCalendarLabel extends ICalendarData {
    /** Specifies if the label is all day or not. */
    allDay?: boolean;
    /** Background color of the label. */
    color?: string;
    /** CSS class for the cell. */
    cellCssClass?: string;
    /** Specifies if an event is editable or not. If false, drag & drop and resize is not allowed. */
    editable?: boolean;
    /** Text of the label */
    text?: string;
    /** Color of the label text. */
    textColor?: string;
    /** The title of the label. */
    title?: string;
    /** Tooltip for the label */
    tooltip?: string;
    /** @hidden */
    id?: string | number;
    /** @hidden */
    resource?: number | string | Array<number | string>;
}
export interface MbscCalendarEvent extends MbscCalendarLabel {
    /** Specifies if the event is all day or not. */
    allDay?: boolean;
    /** A unique id for the event. If not specified, the event will get a generated id. */
    id?: string | number;
    /** Resource or resources of the event. */
    resource?: number | string | Array<number | string>;
    slot?: number | string;
    /** The title of the event. */
    title?: string;
    /** Timezone of the event */
    timezone?: string;
    /** Tooltip for the event */
    tooltip?: string;
    /** @hidden */
    background?: string;
}
export interface MbscCalendarEventData {
    allDay?: boolean;
    allDayText?: string;
    ariaLabel?: string;
    color?: string;
    cssClass?: string;
    currentResource?: MbscResource;
    date: number;
    end?: string;
    endDate: Date;
    html?: any;
    id?: any;
    isMultiDay?: boolean;
    lastDay?: string;
    original?: MbscCalendarEvent;
    position?: any;
    resource?: number | string | Array<number | string>;
    showText?: boolean;
    slot?: number | string;
    start?: string;
    startDate: Date;
    style?: any;
    title?: string;
    tooltip?: string;
    uid?: string | number;
}
