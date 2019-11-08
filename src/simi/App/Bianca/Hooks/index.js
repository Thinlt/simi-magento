import React, {useState, useEffect} from 'react';

const useWindowSize = (initial = {width: window.innerWidth, height: window.innerHeight}) => {
    const [size, setSize] = useState(initial);

    const onChangeSize = () => {
        setSize({
            width: window.innerWidth,
            height: window.innerHeight
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