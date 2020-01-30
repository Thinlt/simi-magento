import React from 'react'
import Modal from 'react-responsive-modal'
import Identify from 'src/simi/Helper/Identify'
require('./getOtpModal.scss');

const $ = window.$
class GetOtpModal extends React.Component {
    constructor(props) {
        super(props)
    }

    componentDidMount() {

    }

    render() {

        const { openGetModal, closeGetModal } = this.props

        return (
            <Modal 
                modalId="modal-get-otp-register"
                open={openGetModal} onClose={closeGetModal}
                classNames={{overlay: Identify.isRtl()?"rtl-wrap-modal":""}}
            >
                <div className="title">
                    {Identify.__('verify your mobile number'.toUpperCase())}
                </div>
                <div className="description">
                    {Identify.__('Click the button below to verify your phone number ')}
                    <span className="bold-number">{localStorage.getItem('numberphone_register')}</span>
                </div>
                <button
                    onClick={this.props.senOtpRegister}
                    className="btn-show-get-modal"
                >
                    {Identify.__('get otp'.toUpperCase())}
                </button>
            </Modal>
        )
    }
}

export default GetOtpModal