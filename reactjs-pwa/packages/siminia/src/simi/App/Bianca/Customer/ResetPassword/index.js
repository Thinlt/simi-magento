import React from 'react';
import { Form } from 'informed';
import { validators } from './validators';
import Identify from 'src/simi/Helper/Identify';
import TextInput from 'src/components/TextInput';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import { showToastMessage } from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';
import { smoothScrollToView } from 'src/simi/Helper/Behavior';
import { createPassword } from 'src/simi/Model/Customer';
import { Link } from 'react-router-dom';
require('./index.scss');

const $ = window.$;
class ResetPassword extends React.Component {
	constructor(props) {
		super(props);
		this.token = false;
		let params = new URL(window.location.href);
		if (params && params.searchParams.get('token')) {
			this.token = params.searchParams.get('token');
		}
		if (!this.token) {
			console.log('no token');
		}
	}

	render() {
		const handleSubmit = (values) => {
			showFogLoading();
			if (!this.token) {
				console.log('no token');
				let msg = Identify.__('Your link reset password is invalid !');
				showToastMessage(msg);
			} else {
				console.log('have token : ' + this.token);
				createPassword(createDone, { rptoken: this.token, password: values.password });
			}
		};

		const createDone = (data) => {
			if (data.errors) {
				console.log('nooo');
				let errorMsg = '';
				if (data.errors.length) {
					data.errors.map((error) => {
						errorMsg += error.message;
					});
					hideFogLoading();
					showToastMessage(errorMsg);
				}
			} else {
				hideFogLoading();
				smoothScrollToView($('#id-message'));
				let successMsg = Identify.__('Updated new password successfully !');
				// reset form
				$('.form')[0].reset();
				// clear user name and password save in local storage
				let savedUser = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_email');
				let savedPassword = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_password');
				if (savedUser && savedPassword) {
					localStorage.removeItem('user_email');
					localStorage.removeItem('user_password');
				}
				showToastMessage(successMsg);
				// this.props.toggleMessages([{ type: 'success', message: successMsg, auto_dismiss: true }]);
			}
		};

		return (
			<div className="forgot-password-customer" id="id-message">
				{TitleHelper.renderMetaHeader({
					title: Identify.__('Reset Customer Password')
				})}
				<div className="title">
					<span>{Identify.__('reset password'.toUpperCase())}</span>
				</div>
				<div className="wrap">
					<div className="title-form">{Identify.__('create new password?'.toUpperCase())}</div>
					<p className="description-form">{Identify.__('Enter a new password for account:')}</p>
					<Form className="form" getApi={this.setFormApi} onSubmit={handleSubmit}>
						<div className="newPassword">
							<div className="title-input">{Identify.__('NEW PASSWORD *')}</div>
							<TextInput
								style={{ paddingLeft: '56px' }}
								field="password"
								type="password"
								autoComplete="new-password"
								validate={validators.get('password')}
								validateOnBlur
							/>
						</div>
						<div className="confirmPassword">
							<div className="title-input">{Identify.__('CONFIRM NEW PASSWORD *')}</div>
							<TextInput
								style={{ paddingLeft: '56px' }}
								field="confirm"
								type="password"
								validate={validators.get('confirm')}
								validateOnBlur
							/>
						</div>
						<div className="resetPassword">
							<button
								priority="high"
								className="resetPassBtn"
								type="submit"
								style={{ backgroundColor: '#101820', color: '#fff' }}
							>
								{Identify.__('Reset My Password'.toUpperCase())}
							</button>
						</div>
						<span className="back special-back" id="btn-back">
							<Link to={'/'}>{Identify.__('back'.toUpperCase())}</Link>
						</span>
					</Form>
				</div>
			</div>
		);
	}
}

const mapDispatchToProps = {
	toggleMessages
};

export default connect(null, mapDispatchToProps)(ResetPassword);
