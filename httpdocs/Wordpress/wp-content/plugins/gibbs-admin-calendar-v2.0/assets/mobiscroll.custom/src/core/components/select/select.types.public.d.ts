import { IBaseEvent } from '../../base';
import { IScrollerProps } from '../scroller/scroller.types';
export interface MbscSelectOnFilterEvent extends IBaseEvent {
    filterText: string;
}
export interface MbscSelectOptions extends IScrollerProps {
    /** @hidden */
    clearIcon?: string;
    /** @hidden */
    inputElement?: HTMLElement;
    /** @hidden */
    inputTyping?: boolean;
    /** @hidden */
    fullScreen?: boolean;
    /** @hidden */
    selectElement?: HTMLSelectElement;
    /** @hidden */
    valid?: any[];
    /**
     * Specifies the selectable options for the component.
     *
     * When it's an array of strings, the selectable options will be the items of the array.
     * The strings will appear on the picker, and the selected values will be the strings themselves.
     *
     * When it's an array of objects, the objects can have the following properties:
     * - `text`: *string* - The text of the option.
     * - `value`: *any* - The value of the option.
     * - `group`: *string* - The group name in case of grouped options.
     * - `disabled`: *boolean* - The disabled state of the option. Disabled options cannot be selected.
     *
     * @defaultValue undefined
     */
    data?: any[];
    /**
     * If `false`, the down arrow icon is hidden.
     * @defaultValue true
     */
    dropdown?: boolean;
    /**
     * If `true`, it turns filtering on. A filter input will be rendered above the select options.
     * Typing in the input will filter the select options and will also trigger the [onFilter](#event-onFilter) event.
     *
     * The default behavior is based on the presence of the search term in the option text.
     * The [onFilter](#event-onFilter) event can be used to prevent the default filtering and customize the experience.
     *
     * @defaultValue false
     */
    filter?: boolean;
    /**
     * An array of values that are invalid. Invalid options cannot be selected, and show up as disabled on the select.
     * @defaultValue undefined
     */
    invalid?: any[];
    /**
     * If `true` and the select has groups, two columns will be rendered on the picker,
     * the first containing the group labels, and the second one the options.
     * Groups can be specified either by `optgroup` elements, when the data comes from the html markup,
     * or by using the `group` property of the option objects, when the [data](#opt-data) option is used.
     * @defaultValue false
     */
    showGroupWheel?: boolean;
    /**
     * Text for the empty state of the select. The select will show this message,
     * when [filtering](#opt-filter) is turned on and there are no results for the searched text.
     * @defaultValue 'No results'
     * @group Localizations
     */
    filterEmptyText?: string;
    /**
     * Placeholder text for the filter input, when [filtering](#opt-filter) is turned on.
     * @defaultValue 'Search'
     * @group Localizations
     */
    filterPlaceholderText?: string;
    /**
     * @event
     * Triggered when the value of the filter input changes.
     * Filtering can be turned on with the [filter](#opt-filter) option.
     *
     * To fully customize the filtering, the default behavior can be prevented by returning `false` from the handler function.
     *
     * @param args The event argument with the following properties:
     *    - `filterText`: *string* - The value of the filter input.
     * @param inst The component instance.
     */
    onFilter?(args: MbscSelectOnFilterEvent, inst: any): boolean;
}
