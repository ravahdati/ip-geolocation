// External Dependencies
import React, { Component } from 'react';

// Internal Dependencies
import './styles/backend-style.css';

class IPGeoDiviModule extends Component {

  static slug = 'ip_geolocation_module';

  render() {
    const module_data  = window.IpgeoDiviBuilderData.i10n.ip_geolocation_module;

    return (
      <p className='ip_geo_module'>
        <span className='title'>{ module_data.title }</span>
        <span className='description'>{ module_data.description }</span>
      </p>
    );
  }
}

export default IPGeoDiviModule;
