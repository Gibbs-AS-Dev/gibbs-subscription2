import { MbscAlertOptions, MbscConfirmOptions, MbscPromptOptions, MbscSnackbarOptions, MbscToastOptions } from '../../../preact/../core/components/notifications/notifications.types';
import { createElement } from '../../../preact/renderer';
import './notifications.scss';
/**
 * Returns content for alert and confirm.
 * @hidden
 * @param options
 */
export declare function getAlertContent(options: MbscAlertOptions | MbscConfirmOptions): createElement.JSX.Element;
/**
 * Returns content for prompt
 * @hidden
 * @param options
 */
export declare function getPromptContent(options: MbscPromptOptions, onInputChange: (event: any) => void, getVal: () => any): createElement.JSX.Element;
/**
 * Returns content for toast.
 * @hidden
 * @param options
 */
export declare function getToastContent(options: MbscToastOptions): createElement.JSX.Element;
/**
 * Returns content for snackbar.
 * @hidden
 * @param options
 */
export declare function getSnackbarContent(options: MbscSnackbarOptions, onButtonClick: () => void): createElement.JSX.Element;
export declare function toast(options: MbscToastOptions): Promise<undefined>;
export declare function snackbar(options: MbscSnackbarOptions): Promise<undefined>;
export declare function alert(options: MbscAlertOptions): Promise<undefined>;
export declare function confirm(options: MbscConfirmOptions): Promise<boolean>;
export declare function prompt(options: MbscPromptOptions): Promise<string | null>;
