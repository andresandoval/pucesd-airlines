-- from SYSTEM

create tablespace TS_AIRLINES datafile 'C:/ts_airlines.dbf' size 1M
autoextend on next 200K maxsize 1400K
default storage (initial 16K next 16K); 

create temporary tablespace TTS_AIRLINES tempfile 'C:/tts_airlines.dbf' size 1M
autoextend on next 200K maxsize 1400K
extent management local uniform size 100K;

create user AIRLINES identified by AIRLINES
default tablespace TS_AIRLINES
temporary tablespace TTS_AIRLINES
quota unlimited on TS_AIRLINES;

grant create session to AIRLINES;
grant create table to AIRLINES;
grant create procedure to AIRLINES;
grant create trigger to AIRLINES;

-- from AIRLINES
-- TABLES
create table usuario(
    idUsuario varchar2(25) primary key not null,
    nombresUsuario varchar2(20) not null,
    apellidosUsuario varchar2(20) not null,
    ciUsuario numeric unique not null,
    generoUsuario varchar2(10) not null,
    fechaNacimientoUsuario date not null,
    nacionalidadUsuario varchar2(15) not null,
    eMailUsuario varchar2(45) not null,
    telefonoUsuario varchar2(20) not null,
    tipoUsuario varchar2(15) not null,
    passwordUsuario varchar2(32) not null
);

create table ruta(
    idRuta varchar2(25) primary key not null,
    idAvion varchar2(25) not null,
    ciPiloto numeric not null,
    destinoRuta varchar2(25) not null,
    fechaHoraRuta date not null,
    andenRuta numeric not null,
    lugaresDisponiblesRuta numeric not null,
    precioRuta numeric not null,
    foreign key(idAvion) references avion(idAvion),
    foreign key(ciPiloto) references piloto(ciPiloto)
);

create table avion(
    idAvion varchar2(25) primary key not null,
    marcaAvion varchar2(25) not null,
    modeloAvion varchar2(30) not null,
    capacidadAvion numeric not null,
    observacionesAvion varchar2(1000) not null
);

create table piloto(
    ciPiloto numeric primary key not null,
    nombresPiloto varchar2(20) not null,
    apellidosPiloto varchar2(20) not null,
    fechaNacimientoPiloto date not null,
    nacionalidadPiloto varchar2(15) not null,
    eMailPiloto varchar2(45) not null,
    telefonoPiloto varchar2(20) not null   
); 

create table cliente(
    ciCliente numeric primary key not null,
    nombreCliente varchar2(25) not null,
    apellidoCliente varchar2(25) not null,
    mailCliente varchar2(50) unique not null,
    passwordCliente varchar2(32) not null
);

create table reservacion(
    idReservacion numeric primary key not null,
    idRuta varchar2(25) not null,
    idAvion varchar2(25) not null,
    ciPiloto numeric not null,
    ciCliente numeric not null,
    fechaReservacion date not null,
    asientoReservacion numeric not null,
    estadoReservacion varchar2(25) not null,
    foreign key(idRuta) references ruta(idRuta),
    foreign key(idAvion) references avion(idAvion),
    foreign key(ciPiloto) references piloto(ciPiloto),
    foreign key(ciCliente) references cliente(ciCliente)
);

create table factura(
    idFactura numeric primary key not null,
    idReservacion numeric not null,
    idRuta varchar2(25) not null,
    idAvion varchar2(25) not null,
    ciPiloto numeric not null,
    ciCliente numeric not null,
    fechaFacturacion date not null,
    ticketDepositoFacturacion varchar2(25) not null,
    foreign key(idReservacion) references reservacion(idReservacion),
    foreign key(idRuta) references ruta(idRuta),
    foreign key(idAvion) references avion(idAvion),
    foreign key(ciPiloto) references piloto(ciPiloto),
    foreign key(ciCliente) references cliente(ciCliente)
);
commit;


--PROCEDURES

--loginUsuario
create or replace
function loginUsuario(user_ usuario.idUsuario%TYPE, pass_ usuario.passwordUsuario%TYPE) return usuario.tipoUsuario%TYPE
as
    tipo_usuario usuario.tipoUsuario%TYPE;
begin
    select usuario.tipoUsuario into tipo_usuario from usuario where usuario.idUsuario=user_ and usuario.passwordUsuario=pass_;
    return tipo_usuario;
end;
/

asss:= LOGINUSUARIO('Andres',  'asas');

select loginUsuario('','') from dual;