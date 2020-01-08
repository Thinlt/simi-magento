import React, {useState, useEffect} from 'react';

const useWindowSize = (initial = {width: document.body.clientWidth, height: document.body.clientHeight}) => {
    const [size, setSize] = useState(initial);

    const onChangeSize = () => {
        setSize({
            width: document.body.clientWidth,
            height: document.body.clientHeight
        })
    }

    useEffect(() => {
        window.addEventListener('resize', onChangeSize);
        return () => {
            window.removeEventListener('resize', onChangeSize);
        }
    }, []);

    return size;
}


export default useWindowSize;