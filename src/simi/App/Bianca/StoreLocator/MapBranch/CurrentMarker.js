import React from 'react';

const CurrentMarker = (props) => {
    const { color, name } = props;
    return (
        <div>
            <div
                className="marker-pin bounce"
                style={{ backgroundColor: color, cursor: 'pointer' }}
                title={name}
            />
            <div className="marker-pulse" />
        </div>
    );
};

export default CurrentMarker;