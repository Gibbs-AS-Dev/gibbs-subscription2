import { DateType } from '../../util/datetime';
import { MbscCalendarOptions } from '../calendar/calendar';
import { MbscDatetimeOptions } from '../datetime/datetime';
import { TDatepickerControl } from './datepicker';
export interface MbscDatepickerOptions extends MbscDatetimeOptions, MbscCalendarOptions {
    /** @hidden */
    clearIcon?: string;
    /** @hidden */
    dateFormatLong?: string;
    /** @hidden */
    defaultValue?: any;
    /** @hidden */
    eventText?: string;
    /** @hidden */
    eventsText?: string;
    /** @hidden */
    modules?: any[];
    /** @hidden */
    showControls?: boolean;
    /** @hidden */
    tabs?: boolean | 'auto';
    /** @hidden */
    weekText?: string;
    /**
     * Number of months or weeks to display.
     * The view starts from a reference date defined by [refDate](#opt-refDate) option.
     * @defaultValue 1
     */
    calendarSize?: number;
    /**
     * List of controls to display on the picker. Possible values:
     * - `['calendar']`
     * - `['calendar', 'time']`
     * - `['date']`
     * - `['datetime']`
     * - `['date', 'time']`
     * - `['time']`
     * - `['timegrid']`
     * @defaultValue ['calendar']
     */
    controls?: TDatepickerControl[];
    /**
     * Default selection which appears on the picker. The provided value will not be set as picker value, it's only a pre-selection.
     *
     * If not provided, the default selection will be the current date and/or time.
     *
     * If `null` is passed, in case of the calendar control there will be no selected value, in case of scroller controls the current date
     * and time will still be displayed on the selected line, as an empty value cannot be displayed on the scroller.
     *
     * @defaultValue undefined
     */
    defaultSelection?: any;
    /**
     * Sets the input for the end date.
     *
     * When using the datepicker to [select a range](#opt-select), it can be used with one, two or no inputs.
     * When the `endInput` is specified, it will put the range end part of the selection to that input.
     * Similarly the input for the range start can be specified using the [startInput](#opt-startInput) option.
     *
     * @defaultValue undefined
     */
    endInput?: any;
    /**
     * Sets the first day of the selection, when `'preset-range'` [selection](#opt-select) is used.
     * It takes a number representing the day of the week, * Sunday is 0, Monday is 1, etc.
     *
     * If not specified, it defaults to the first day of the week defined by the [localization](#localization-locale).
     *
     * The length of the selection can be controlled with the [selectSize](#opt-selectSize) option.
     *
     * @defaultValue undefined
     */
    firstSelectDay?: number;
    /**
     * In case of [range selection](#opt-select), specifies if invalid dates are allowed between the start and end dates.
     *
     * When set to `false`, the end selection will be limited to the next invalid date from the selected start date.
     *
     * For example, when designing a booking system, a possible solution for already booked dates is to set those dates to invalid.
     * When considering check-in and check-out selections with the datepicker, in some cases it is desireable to be able to check out
     * on dates that already have a check-in. In this case, those dates would be disabled. Using the
     * [rangeEndInvalid](#opt-rangeEndInvalid) option it can be allowed that the first invalid day after the start date can be selected
     * as end date.
     *
     * @defaultValue false
     */
    inRangeInvalid?: boolean;
    /**
     * Sets the maximum range that can be selected. When selecting a date range without the time part, it sets the maximum number of days
     * the selected range can contain. When there is a time part in the selection, it sets the maximum range in milliseconds.
     *
     * @defaultValue undefined
     */
    maxRange?: number;
    /**
     * It sets the maximum time that is selectable on the time or the timegrid [control](#opt-controls).
     *
     * When there is a date part of the selection, the maximum is applied to each day. For example `maxTime: '18:00'`
     * will limit the time part of the selection to at most 18 o'clock each day,
     * in contrast to the [max](#opt-max) option, which will limit the date part of the selection as well.
     * For example: `max: '2021-08-22T18:00` will limit the selection to August 22nd 18 o'clock,
     * but will allow selection of times past 18 o'clock on dates before August 22nd.
     *
     * :::info
     * This option can't be used with the `'datetime'` [control](#opt-controls).
     * :::
     * @defaultValue undefined
     */
    maxTime?: DateType;
    /**
     * Sets the minimum range that can be selected. When selecting a date range without the time part, it sets the minimum number of days
     * the selected range can contain. When there is a time part in the selection, it sets the minimum range in milliseconds.
     *
     * @defaultValue undefined
     */
    minRange?: number;
    /**
     * It sets the minimum time that is selectable on the time or the timegrid [control](#opt-controls).
     *
     * When there is a date part of the selection, the minimum is applied to each day. For example `minTime: '08:00'`
     * will limit the time part of the selection to at least 8 o'clock each day.
     * in contrast to the [min](#opt-min) option, which will limit the date part of the selection as well.
     * For example: `min: '2021-08-22T08:00` will limit the selection to August 22nd 8 o'clock,
     * but will allow selection of times earlier than 8 o'clock on dates after August 22nd.
     *
     * :::info
     * This option can't be used with the `'datetime'` [control](#opt-controls).
     * :::
     * @defaultValue undefined
     */
    minTime?: DateType;
    /**
     * When `true`, it enables the end date to be selected on the first invalid date that comes after the selected start date.
     *
     * For example, When designing a booking system, a possible solution for already booked dates is to set those dates to invalid.
     * When considering check-in and check-out selections with the datepicker, in some cases it is desireable to be able to check out
     * on dates that already have a check-in. In this case, those dates would be disabled.
     * With this option it can be enabled, so the selection's end can be on the same day the first invalid is.
     *
     * :::info
     * When the [inRangeInvalid](#opt-inRangeInvalid) option is set to `true` (default), this option is ignored, so make sure to turn
     * that off too if you want to use this one.
     * :::
     *
     * @defaultValue false
     */
    rangeEndInvalid?: boolean;
    /**
     * When selecting a range on the calendar control, it is optional to highlight the dates between the start and end date.
     *
     * By default, the dates are highlighted between the start and end values. When the highlight is turned off, only the start
     * and end dates are shown as selected on the calendar.
     *
     * On desktop devices where a cursor is available, hovering the calendar days also help to visualize the selecting range.
     * The hover styling is also turned off, when the range is not highlighted.
     *
     * @defaultValue true
     */
    rangeHighlight?: boolean;
    /**
     * In terms of value selection, the datepicker can be used to select a single date/time or multiple dates/times, as well as a date range
     * or a time range. It is also possible to select a week or a part of the week as a range.
     *
     * The select option defines if the picker is used for selecting independent dates or a range.
     *
     * Possible values are:
     * - `'date'` - Used for single or multiple date/time selection.
     * - `'range'` - Used for date range or time range selection.
     * - `'preset-range'` - Used for a week range selection.
     *
     * @defaultValue 'date'
     */
    select?: 'date' | 'range' | 'preset-range';
    /**
     * If `true` and [multiple selection](#opt-selectMultiple) is enabled, the number of selected items will be displayed in the header.
     *
     * @defaultValue false
     */
    selectCounter?: boolean;
    /**
     * Sets the length of the selection in days when [preset-range selection](#opt-select) is used.
     *
     * The start of the selection can be set using the [firstSelectDay](#opt-firstSelectDay) option
     * and will have the specified length.
     *
     * It defaults to a full week (7 days).
     *
     * @defaultValue 7
     */
    selectSize?: number;
    /**
     * Show or hide the range labels.
     *
     * When [selecting a range](#opt-select), a start and end label is displayed on the picker.
     * These labels indicate which part of the range are we selecting (start or end).
     * The labels can also be used to switch the active selection from start to end or vice versa.
     *
     * The [start label text](#localization-rangeStartLabel) and [end label text](#localization-rangeEndLabel) as well as the
     * [start value placeholder](#localization-rangeStartHelp) and the [end value placeholder](#localization-rangeEndHelp)
     * can be customized.
     *
     * @defaultValue true
     */
    showRangeLabels?: boolean;
    /**
     * Sets the input for the start date.
     *
     * When using the datepicker to [select a range](#opt-select), it can be used with one, two or no inputs.
     * When the `startInput` is specified, it will put the range start part of the selection to that input.
     * Similarly the input for the range end can be specified using the [endInput](#opt-endInput) option.
     *
     * @defaultValue undefined
     */
    startInput?: any;
    /**
     * When selecting a range, it specifies the placeholder text for the end value under the [end label](#opt-showRangeLabels).
     * @defaultValue 'Please select'
     * @group Localizations
     */
    rangeEndHelp?: string;
    /**
     * When selecting a range, it specifies the text of the [end label](#opt-showRangeLabels).
     * @defaultValue 'End'
     * @group Localizations
     */
    rangeEndLabel?: string;
    /**
     * When selecting a range, it specifies the placeholder text for the start value under the [start label](#opt-showRangeLabels).
     * @defaultValue 'Please select'
     * @group Localizations
     */
    rangeStartHelp?: string;
    /**
     * When selecting a range, it specifies the text of the [start label](#opt-showRangeLabels).
     * @defaultValue 'Start'
     * @group Localizations
     */
    rangeStartLabel?: string;
    /**
     * @event
     * Triggered when the temporary value is changed.
     * @param args The event argument with the following properties:
     *    - `value`: *DateType | DateType[]* - The selected value.
     * @param inst The component instance.
     */
    onTempChange?(args: {
        value: DateType | DateType[];
    }, inst: any): void;
    /**
     * @event
     * Triggered when the active date changes from start to end or vice versa, in case of range selection mode.
     * @param args The event argument with the following properties:
     *    - `active`: *string* - The activated part of the range, `'start'` or `'end'`.
     * @param inst The component instance.
     */
    onActiveDateChange?(args: {
        active: 'start' | 'end';
    }, inst: any): void;
}
