import React from "react";
import defaultClasses from "./index.css";
export const Colorbtn = props => {
    return (
        <React.Fragment>
            {props.type === "submit" || props.type === "button" ? (
                <button {...props}
                    type={props.type}
                    className={`${defaultClasses['siminia-color-btn']} ${props.className}`}
                >
                    <span className="siminia-btn">{props.text}</span>
                </button>
            ) : (
                <div
                    {...props}
                    className={`${defaultClasses['siminia-color-btn']} ${props.className}`}
                >
                   <span className="siminia-btn">{props.text}</span>
                </div>
            )}
        </React.Fragment>
    );
};
export const Whitebtn = props => {
    return (
        <React.Fragment>
            {props.type === "submit" || props.type === "button" ? (
                <button {...props}
                    type={props.type}
                    className={`${defaultClasses['siminia-white-btn']} ${props.className}`}
                >
                    <span className="siminia-btn">{props.text}</span>
                </button>
            ) : (
                <div
                    {...props}
                    className={`${defaultClasses['siminia-white-btn']} ${props.className}`}
                >
                    <span className="siminia-btn">{props.text}</span>
                </div>
            )}
        </React.Fragment>
    );
};
