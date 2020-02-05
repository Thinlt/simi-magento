import React from 'react'
import Modal from 'react-responsive-modal'
import Identify from 'src/simi/Helper/Identify'
import CountDown from './CountDown'
require('./verifyOtpModal.scss');

const $ = window.$;

class VerifyOtpModal extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            value1: '',
            value2: '',
            value3: '',
            value4: '',
            value5: '',
            value6: ''
        };
    }

    onChange1 = (e) => {
        const re = /^[0-9\b]+$/;
        if (e.target.value === '' || re.test(e.target.value)) {
            this.setState({ value1: e.target.value })
        }
    }
    onChange2 = (e) => {
        const re = /^[0-9\b]+$/;
        if (e.target.value === '' || re.test(e.target.value)) {
            this.setState({ value2: e.target.value })
        }
    }
    onChange3 = (e) => {
        const re = /^[0-9\b]+$/;
        if (e.target.value === '' || re.test(e.target.value)) {
            this.setState({ value3: e.target.value })
        }
    }
    onChange4 = (e) => {
        const re = /^[0-9\b]+$/;
        if (e.target.value === '' || re.test(e.target.value)) {
            this.setState({ value4: e.target.value })
        }
    }
    onChange5 = (e) => {
        const re = /^[0-9\b]+$/;
        if (e.target.value === '' || re.test(e.target.value)) {
            this.setState({ value5: e.target.value })
        }
    }
    onChange6 = (e) => {
        const re = /^[0-9\b]+$/;
        if (e.target.value === '' || re.test(e.target.value)) {
            this.setState({ value6: e.target.value })
        }
    }

    componentDidMount() {
        $('#form-verify-otp .otp1').focus();
        if (Identify.isRtl()) {
            $('#verify-otp-modal').addClass('rtl-modal')
        }
    }

    render() {

        const { openVerifyModal, closeVerifyModal } = this.props
        const verifyOtp = (e) => {
            e.preventDefault()
            $('.otp-form .error').css('display', 'none')
            let bool1 = $('.otp1').val().length == 1;
            let bool2 = $('.otp2').val().length == 1;
            let bool3 = $('.otp3').val().length == 1;
            let bool4 = $('.otp4').val().length == 1;
            let bool5 = $('.otp5').val().length == 1;
            let bool6 = $('.otp6').val().length == 1;
            let valid = bool1 && bool2 && bool3 && bool4 && bool5 && bool6;
            if (!valid) {
                $('#verify-otp-modal .error').css('display', 'block')
            } else {
                $('.otp-form .error').css('display', 'none')
                let otp1 = $('.otp1').val()
                let otp2 = $('.otp2').val()
                let otp3 = $('.otp3').val()
                let otp4 = $('.otp4').val()
                let otp5 = $('.otp5').val()
                let otp6 = $('.otp6').val()
                let otp = otp1 + otp2 + otp3 + otp4 + otp5 + otp6;
                // save otp to localStorage
                localStorage.setItem('login_otp', otp)
                // call api
                this.props.callApi(localStorage.getItem('numberphone_otp'))
                // close modal
                this.props.closeVerifyModal()
            }



        }

        const jumbTo2 = () => {
            $('#form-verify-otp .otp2').focus();
        }
        const jumbTo3 = () => {
            $('#form-verify-otp .otp3').focus();
        }
        const jumbTo4 = () => {
            $('#form-verify-otp .otp4').focus();
        }
        const jumbTo5 = () => {
            $('#form-verify-otp .otp5').focus();
        }
        const jumbTo6 = () => {
            $('#form-verify-otp .otp6').focus();
        }

        return (
            <Modal
                modalId="verify-otp-modal"
                open={openVerifyModal}
                onClose={closeVerifyModal}
                classNames={{ overlay: Identify.isRtl() ? "rtl-wrap-modal" : "" }}
            >
                <div className="title">
                    {Identify.__('verify your mobile number'.toUpperCase())}
                </div>
                <div className="description">
                    {Identify.__('A text message with 6-digit verifycation code has been sent to ')}
                    <span className="bold-number">
                        {localStorage.getItem('numberphone_otp') ? localStorage.getItem('numberphone_otp') : localStorage.getItem('numberphone_register')}
                    </span>
                </div>
                <form onSubmit={verifyOtp} method="post" id="form-verify-otp">
                    <div className="otp-form">
                        <input className="otp otp1" name="otp1" maxLength="1" value={this.state.value1} onChange={this.onChange1} onKeyUp={jumbTo2} />
                        <input className="otp otp2" name="otp2" maxLength="1" value={this.state.value2} onChange={this.onChange2} onKeyUp={jumbTo3} />
                        <input className="otp otp3" name="otp3" maxLength="1" value={this.state.value3} onChange={this.onChange3} onKeyUp={jumbTo4} />
                        <input className="otp otp4" name="otp4" maxLength="1" value={this.state.value4} onChange={this.onChange4} onKeyUp={jumbTo5} />
                        <input className="otp otp5" name="otp5" maxLength="1" value={this.state.value5} onChange={this.onChange5} onKeyUp={jumbTo6} />
                        <input className="otp otp6" name="otp6" maxLength="1" value={this.state.value6} onChange={this.onChange6} />
                    </div>
                    <button type="submit">
                        {Identify.__('verify'.toUpperCase())}
                    </button>
                </form>
                <div className="error">
                    {Identify.__('Invalid otp code !')}
                </div>
                <div className="count-down">
                    {Identify.__('Resend after')}<CountDown time={120} />
                </div>
            </Modal>
        )
    }
}

export default VerifyOtpModal