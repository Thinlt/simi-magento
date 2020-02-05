import React from 'react'
import Identify from 'src/simi/Helper/Identify'
class CountDown extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            h: 0,
            m: 0,
            s: 0
        }
        this.time = this.props.time * 1000;

    }

    componentDidMount() {
        this.startTime()
        this.timer = setInterval(() => this.startTime(), 1000)
    }

    componentWillUnmount() {
        clearInterval(this.timer)
    }

    startTime = () => {
        let time = this.time;
        let h = Math.floor((time % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let m = Math.floor((time % (1000 * 60 * 60)) / (1000 * 60));
        let s = Math.floor((time % (1000 * 60)) / 1000);
        this.setState({ h, m, s })
        this.time = this.time - 1000
        if (time < 0) {
            clearInterval(this.timer)
        }
    }

    render() {
        const { h, m, s } = this.state;
        return (
            <div className="time-result" style={{ display: 'inline-block', marginLeft: 10, color: '#101820' }}>
                {`${m} `}{Identify.__('min')} {`${s} `}{Identify.__('seconds')}
            </div>
        )
    }
}
export default CountDown
