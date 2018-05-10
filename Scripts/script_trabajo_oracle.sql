-- from SYSTEM

create tablespace TS_BOLETERIA datafile 'C:/ts_boleteria.dbf' size 1M
autoextend on next 200K maxsize 1400K
default storage (initial 16K next 16K); 

create temporary tablespace TTS_BOLETERIA tempfile 'C:/tts_boleteria.dbf' size 1M
autoextend on next 200K maxsize 1400K
extent management local uniform size 100K;

create user BOLETERIA identified by BOLETERIA
default tablespace TS_BOLETERIA
temporary tablespace TTS_BOLETERIA
quota unlimited on TS_BOLETERIA;

grant create session to BOLETERIA;
grant create table to BOLETERIA;
grant create procedure to BOLETERIA;
grant create trigger to BOLETERIA;

-- from BOLETERIA
-- TABLES
drop table usuario;
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
    tipoUsuario varchar2(15) not null check(tipoUsuario in ('Administrador','Secretaria','Gerente')),
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

drop table reservacion;
create table reservacion(
    idReservacion numeric primary key not null,
    idRuta varchar2(25) not null,
    idAvion varchar2(25) not null,
    ciPiloto numeric not null,
    ciCliente numeric not null,
    fechaReservacion date not null,
    asientoReservacion numeric not null,
    estadoReservacion varchar2(25) not null check(estadoReservacion in ('Facturado','No facturada')),
    foreign key(idRuta) references ruta(idRuta),
    foreign key(idAvion) references avion(idAvion),
    foreign key(ciPiloto) references piloto(ciPiloto),
    foreign key(ciCliente) references cliente(ciCliente)
);

commit;

drop table reservacionIncumplida;
create table reservacionIncumplida(
  id_ numeric primary key not null,
  id_reservacion_incumplida numeric not null,
  tiempo_retardo varchar(10),
  foreign key (id_reservacion_incumplida) references reservacion(idreservacion)
);

drop table factura;
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


--LOGIN
create or replace
function loginUsuario(user_ usuario.idUsuario%TYPE, pass_ usuario.passwordUsuario%TYPE) return usuario.tipoUsuario%TYPE
as
    tipo_usuario usuario.tipoUsuario%TYPE;
begin
    select usuario.tipoUsuario into tipo_usuario from usuario where usuario.idUsuario=user_ and usuario.passwordUsuario=pass_;
    return tipo_usuario;
end;

--DETALLES
create or replace
function getDetalles(token_ varchar2, id_ varchar2) return varchar2
as
  return_value varchar2(200);
begin
  if(token_ = 'userId')then
    select usuario.idUsuario into return_value from usuario where usuario.idUsuario = id_;
  elsif (token_ = 'userCi') then
    select usuario.ciUsuario into return_value from usuario where usuario.ciUsuario = id_;
  elsif (token_ = 'pilotCi') then
    select piloto.ciPiloto into return_value from piloto where piloto.ciPiloto = id_;
  elsif (token_ = 'planeId') then
    select avion.idAvion into return_value  from avion where avion.idAvion = id_;
  elsif (token_ = 'routeId') then
    select ruta.idRuta into return_value from ruta where ruta.idRuta = id_;
  elsif (token_ = 'planeDetails') then
    select concat('[ Marca :',concat(avion.marcaAvion,concat(' ] [Modelo : ', concat(avion.modeloAvion, concat(' ] [Capacidad : ', concat(avion.capacidadAvion, ' ]') )) ) ))  into return_value from avion where avion.idAvion = id_;
  elsif (token_ = 'pilotDetails') then
    select concat('[ Nombres: ',concat(concat(piloto.nombresPiloto, ' '), concat(piloto.apellidosPiloto, concat(' ] [Nacionalidad : ', concat(piloto.nacionalidadPiloto, ' ]'))))) into return_value from piloto where piloto.ciPiloto = id_;
  end if;
  
  return return_value;
end;

--RECORD-USER
create or replace
function RECORD_USER(TOKEN_ VARCHAR2,ID_ USUARIO.IDUSUARIO%TYPE,NOM_ USUARIO.NOMBRESUSUARIO%TYPE,APP_ USUARIO.APELLIDOSUSUARIO%TYPE,CI_ USUARIO.CIUSUARIO%TYPE,GENER_ USUARIO.GENEROUSUARIO%TYPE,FECH_ USUARIO.FECHANACIMIENTOUSUARIO%TYPE,NACION_ USUARIO.NACIONALIDADUSUARIO%TYPE,MAIL_ USUARIO.EMAILUSUARIO%TYPE,TLF_ USUARIO.TELEFONOUSUARIO%TYPE,TIPO_ USUARIO.TIPOUSUARIO%TYPE,PASS_ USUARIO.PASSWORDUSUARIO%TYPE) RETURN VARCHAR2
as
  NEWPASS_ USUARIO.PASSWORDUSUARIO%TYPE;
BEGIN
  IF (TOKEN_ = 'CREATE') THEN
    INSERT INTO USUARIO(IDUSUARIO, NOMBRESUSUARIO, APELLIDOSUSUARIO, CIUSUARIO, GENEROUSUARIO, FECHANACIMIENTOUSUARIO, NACIONALIDADUSUARIO, EMAILUSUARIO, TELEFONOUSUARIO, TIPOUSUARIO, PASSWORDUSUARIO) VALUES (ID_, NOM_, APP_, CI_, GENER_, FECH_, NACION_, MAIL_, TLF_, TIPO_, PASS_);
    COMMIT;
    RETURN 'TRUE';
  ELSIF(TOKEN_ = 'MODIFY') THEN
    IF(PASS_ = '') THEN
      SELECT USUARIO.PASSWORDUSUARIO INTO NEWPASS_ FROM USUARIO WHERE USUARIO.IDUSUARIO = ID_;
    --ELSE
     -- NEWPASS_ := PASS_;
   -- END IF;    
      UPDATE USUARIO SET USUARIO.NOMBRESUSUARIO= NOM_, USUARIO.APELLIDOSUSUARIO=APP_, USUARIO.GENEROUSUARIO=GENER_, USUARIO.NACIONALIDADUSUARIO=NACION_, USUARIO.EMAILUSUARIO=MAIL_, USUARIO.TELEFONOUSUARIO=TLF_, USUARIO.TIPOUSUARIO=TIPO_, USUARIO.PASSWORDUSUARIO=NEWPASS_ WHERE USUARIO.IDUSUARIO = ID_;
      COMMIT;
    RETURN 'TRUE';
  END IF;
  EXCEPTION
    WHEN OTHERS THEN
    ROLLBACK;
    RETURN 'FALSE';
END;
/

create or replace
function BUSCAR(TOKEN_ varchar2, FILTRO_ varchar2) RETURN SYS_REFCURSOR AS
  rc SYS_REFCURSOR;
begin
  if (TOKEN_ = 'general_user') then
    open rc for select u.idUsuario "Id de usuario", u.nombresUsuario "Nombres", u.apellidosUsuario "Apellidos", u.ciUsuario "Nro. de cedula de identidad", u.tipoUsuario "Tipo de usuario" from usuario u where u.idUsuario like '%'||FILTRO_||'%' or u.nombresUsuario like '%'||FILTRO_||'%' or u.ciUsuario like '%'||FILTRO_||'%' or u.apellidosUsuario like '%'||FILTRO_||'%' order by u.nombresUsuario;
  elsif(TOKEN_ = 'individual_user')then
    open rc for SELECT U.IDUSUARIO "ID DE USUARIO",U.NOMBRESUSUARIO "NOMBRES",U.APELLIDOSUSUARIO "APELLIDOS",U.CIUSUARIO "N�MERO DE CI",U.GENEROUSUARIO "GENERO",U.FECHANACIMIENTOUSUARIO "FECHA DE NACIMIENTO",U.NACIONALIDADUSUARIO "NACIONALIDAD",U.EMAILUSUARIO "E-MAIL",U.TELEFONOUSUARIO "TELEFONO",U.TIPOUSUARIO "TIPO DE USUARIO" FROM USUARIO U WHERE U.IDUSUARIO = FILTRO_;
  elsif(TOKEN_ = 'general_pilot')then
    open rc for select p.ciPiloto "Numero de Ci", concat(p.nombresPiloto,concat (' ', p.apellidosPiloto)) "Nombres y Apellidos", p.fechaNacimientoPiloto "Fecha de Nacimiento", p.nacionalidadPiloto "Nacionalidad", p.eMailPiloto "e-Mail", p.telefonoPiloto "Telefono" from piloto p where p.ciPiloto LIKE '%'|| FILTRO_ || '%' or p.nombresPiloto LIKE '%'|| FILTRO_ || '%' or p.apellidosPiloto LIKE '%'|| FILTRO_ || '%' order by p.ciPiloto, p.nombresPiloto, p.apellidosPiloto;
  elsif(TOKEN_ = 'individual_pilot')then
    open rc for select p.ciPiloto "Numero de Ci", p.nombresPiloto "Nombres",p.apellidosPiloto "Apellidos", p.fechaNacimientoPiloto "Fecha de Nacimiento", p.nacionalidadPiloto "Nacionalidad", p.eMailPiloto "e-Mail", p.telefonoPiloto "Telefono" from piloto p where p.ciPiloto = FILTRO_;
  elsif(TOKEN_ = 'general_plane') then
    open rc for select av.idAvion "Codigo",  av.marcaAvion "Marca",  av.modeloAvion "Modelo",  av.capacidadAvion "Capacidad",  av.observacionesAvion "Observaciones" from avion av where av.idAvion LIKE '%'||FILTRO_||'%' or av.marcaAvion LIKE '%'||FILTRO_||'%' or av.modeloAvion LIKE '%'||FILTRO_||'%' order by av.idAvion;
  elsif(TOKEN_ = 'individual_plane') then
    open rc for select av.idAvion "Codigo", av.marcaAvion "Marca", av.modeloAvion "Modelo", av.capacidadAvion "Capacidad", av.observacionesAvion "Observaciones" from avion av where av.idAvion = FILTRO_;
  elsif(TOKEN_ = 'general_route') then
    open rc for select r.idRuta "Codigo de Ruta", r.destinoRuta "Destino", r.fechaHoraRuta "Fecha y hora de salida", r.andenRuta "And�n de salida", r.lugaresDisponiblesRuta "Nro. lugares disponibles", r.precioRuta "Precio" from ruta r where r.idRuta LIKE '%'||FILTRO_||'%' or r.destinoRuta LIKE '%'||FILTRO_||'%' or r.fechaHoraRuta LIKE '%'||FILTRO_||'%' order by r.idRuta;
  elsif(TOKEN_ = 'individual_route')then
    open rc for select r.idRuta "Codigo de Ruta", r.idAvion "Codigo de Avion",  r.ciPiloto "CI Piloto", r.destinoRuta "Destino", r.fechaHoraRuta "Fecha y hora de salida", r.andenRuta "And�n de salida", r.lugaresDisponiblesRuta "Nro. lugares disponibles", r.precioRuta "Precio" from ruta r where r.idRuta = FILTRO_ order by r.idRuta;
  elsif(TOKEN_ = 'route_by_client')then
    open rc for select r.destinoRuta "Destino",
        r.fechaHoraRuta "Fecha/Hora de salida", 
        av.marcaAvion "Marca del Avion", 
        av.modeloAvion "Modelo del Avion", 
        av.capacidadAvion "Capacidad total del Avion", 
        r.lugaresDisponiblesRuta "Numero de lugares disponibles", 
        p.nombresPiloto ||'  '|| p.apellidosPiloto "Nombre del piloto", 
        p.nacionalidadPiloto "Nacionalidad del piloto", 
        r.precioRuta "Precio" 
        from (ruta r inner join piloto p on r.ciPiloto = p.ciPiloto)  inner join avion av 
        on r.idAvion = av.idAvion where r.idRuta = FILTRO_;
  elsif(TOKEN_ = 'client') then
    open rc for select cli.ciCliente "Numero de CI", cli.nombreCliente||' '||cli.apellidoCliente "Nombres y Apellidos", cli.mailCliente "e-mail" from cliente cli where cli.ciCliente=FILTRO_ or cli.mailCliente = FILTRO_;
  elsif(TOKEN_ = 'individual_reservation')then
    open rc for select res.idReservacion "Codigo de reservacion",
               rta.destinoRuta "Destino",
               rta.fechaHoraRuta "Fecha/hora de salida",
               rta.andenRuta "Numero de anden de salida",
               res.asientoReservacion "Numero de asiento asignado",
               cli.nombreCliente || ' ' || cli.apellidoCliente "Nombres y Apellidos Cliente",
               res.estadoReservacion "Estado de la reservacion"
        from (reservacion res inner join ruta rta on res.idRuta = rta.idRuta)
			inner join cliente cli on res.ciCliente=cli.ciCliente
          where res.idReservacion=FILTRO_;
  elsif(TOKEN_ = 'general_reservation')then
    open rc for select res.idReservacion "Codigo de reservacion",
               rta.destinoRuta "Destino",
               rta.fechaHoraRuta "Fecha/hora de salida",
               rta.andenRuta "Numero de anden de salida",
               cli.nombreCliente || ' ' || cli.apellidoCliente "Nombres y Apellidos Cliente",
               res.estadoReservacion "Estado de la reservacion"
        from (reservacion res inner join ruta rta on res.idRuta = rta.idRuta)
			inner join cliente cli on res.ciCliente=cli.ciCliente
          where res.idReservacion like '%' || FILTRO_ || '%' order by res.idReservacion asc;          
  elsif(TOKEN_ = 'individual_factura')then
        open rc for select f.idFactura "Codigo de factura",
               f.idReservacion "Codigo de reservacion",
               f.fechaFacturacion "Fecha/hora de facturacion",
               rta.precioRuta "Valor $",
               res.ciCliente "Nro. CI del Cliente",
               cli.nombreCliente "Nombre del Cliente"
        from reservacion res inner join factura f on f.idReservacion = res.idReservacion
                              inner join ruta rta on res.idRuta = rta.idRuta
                              inner join cliente cli on  res.ciCliente = cli.ciCliente
        where f.idFactura = FILTRO_;
              
  elsif(TOKEN_ = 'individual_ticket')then
      open rc for select res.idReservacion "Codigo de reservacion",
               res.ciCliente "Nro. CI del Cliente",
               cli.nombreCliente "Nombre del Cliente",
               rta.fechaHoraRuta "Fecha/hora de salida",
               rta.andenRuta "Nro. and�n de salida",
               res.asientoReservacion "Asiento asignado",
               rta.idAvion "Codigo de avion"               
      from reservacion res inner join ruta rta on res.idRuta = rta.idRuta
                           inner join cliente cli on res.ciCliente = cli.ciCliente
      where res.idReservacion = FILTRO_;
  elsif(TOKEN_ = 'general_res_incumplidas') then
    open rc for select res.idReservacion "Codigo de reservacion",
               res.ciCliente "Nro. CI del Cliente",
               cli.nombreCliente "Nombre del Cliente",
               rta.fechaHoraRuta "Fecha/hora de salida",
               rta.andenRuta "Nro. and�n de salida",
               rta.idAvion "Codigo de avion",
               inc.tiempo_retardo||' dias' "Tiempo de Retardo"
      from reservacionIncumplida inc inner join reservacion res on inc.id_reservacion_incumplida = res.idreservacion
                            inner join ruta rta on res.idRuta = rta.idRuta
                           inner join cliente cli on res.ciCliente = cli.ciCliente
      where inc.ID_RESERVACION_INCUMPLIDA LIKE '%' || FILTRO_ || '%';
  elsif(TOKEN_ = 'individual_res_incumplidas') then
    open rc for select res.idReservacion "Codigo de reservacion",
               res.ciCliente "Nro. CI del Cliente",
               cli.nombreCliente "Nombre del Cliente",
               rta.fechaHoraRuta "Fecha/hora de salida",
               rta.andenRuta "Nro. and�n de salida",
               rta.idAvion "Codigo de avion",
               inc.tiempo_retardo||' dias' "Tiempo de Retardo"
      from reservacionIncumplida inc inner join reservacion res on inc.id_reservacion_incumplida = res.idreservacion
                            inner join ruta rta on res.idRuta = rta.idRuta
                           inner join cliente cli on res.ciCliente = cli.ciCliente
      where inc.ID_RESERVACION_INCUMPLIDA = FILTRO_;
  elsif(TOKEN_ = 'general_event') then
    open rc for select eve.idevento "Id del Evento", eve.fechaevento "Fecha del evento", eve.descripcionevento "Descripcion del evento" from evento eve where eve.descripcionevento like '%'||FILTRO_||'%' or eve.idevento like '%'||FILTRO_||'%';
  elsif(TOKEN_ = 'individual_event') then
    open rc for select eve.idevento "Id del Evento", eve.fechaevento "Fecha del evento", eve.descripcionevento "Descripcion del evento" from evento eve where eve.idevento = FILTRO_;
  end if;
  RETURN rc;
end;
/

commit;






create or replace
function buscarRutasCliente(destino ruta.destinoRuta%TYPE, fecha ruta.fechaHoraRuta%TYPE) return SYS_REFCURSOR  as
rc SYS_REFCURSOR;
begin
  open rc for select ruta.idRuta "ID", ruta.destinoRuta "Destino", ruta.fechaHoraRuta "Fecha/Hora de salida", ruta.lugaresDisponiblesRuta "Lugares disponibles", ruta.precioRuta "Precio"
      from ruta where ((ruta.destinoRuta = destino or ruta.fechaHoraRuta like '%'||fecha||'%') and ruta.precioRuta > 0);
  return rc;
end;
/
select buscarRutasCliente('Quito', '01/01/2013') from dual;

create or replace
function ELIMINAR(TOKEN_ varchar2, ID_ varchar2) RETURN varchar2
as
begin
  if (TOKEN_ = 'user') then
    delete from USUARIO U where U.IDUSUARIO = ID_;
    commit;
    return 'TRUE';
  elsif(TOKEN_ = 'pilot')then
    delete from piloto p where p.cipiloto = ID_;
    commit;
    return 'TRUE';
  elsif(TOKEN_ = 'plane')then
    delete from avion av where av.idavion = ID_;
    commit;
    return 'TRUE';
  elsif(TOKEN_ = 'route')then
    delete from ruta r where r.idruta = ID_;
    commit;
    return 'TRUE';
  end if;
  Exception
    when others then
      rollback;
      return 'FALSE';
end;
/

CREATE OR REPLACE
FUNCTION RECORD_PILOTO(TOKEN_ VARCHAR2, CI_ PILOTO.CIPILOTO%TYPE, NOM_ PILOTO.NOMBRESPILOTO%TYPE, APP_ PILOTO.APELLIDOSPILOTO%TYPE, FECH_ PILOTO.FECHANACIMIENTOPILOTO%TYPE, NACIO_ PILOTO.NACIONALIDADPILOTO%TYPE, MAIL_ PILOTO.EMAILPILOTO%TYPE, TELF_ PILOTO.TELEFONOPILOTO%TYPE) RETURN VARCHAR2
AS  
BEGIN
  IF (TOKEN_ = 'CREATE') THEN
    INSERT INTO PILOTO(CIPILOTO, NOMBRESPILOTO, APELLIDOSPILOTO, FECHANACIMIENTOPILOTO, NACIONALIDADPILOTO, EMAILPILOTO, TELEFONOPILOTO) VALUES(CI_, NOM_, APP_, FECH_, NACIO_, MAIL_, TELF_);
    COMMIT;
    RETURN 'TRUE';
  ELSIF(TOKEN_ = 'MODIFY') THEN
    update piloto p set p.nombresPiloto = NOM_, p.apellidosPiloto=APP_, p.nacionalidadPiloto=NACIO_, p.eMailPiloto=MAIL_, p.telefonoPiloto=TELF_ where p.ciPiloto = CI_;
    COMMIT;
    RETURN 'TRUE';
  END IF;
  EXCEPTION
    WHEN OTHERS THEN
    ROLLBACK;
    RETURN 'FALSE';
END;
/




CREATE OR REPLACE
FUNCTION RECORD_AVION(TOKEN_ VARCHAR2, ID_ AVION.IDAVION%TYPE, MARCA_ AVION.MARCAAVION%TYPE, MODELO_ AVION.MODELOAVION%TYPE, CAPAC_ AVION.CAPACIDADAVION%TYPE, OBSER_ AVION.OBSERVACIONESAVION%TYPE) RETURN VARCHAR2
AS  
BEGIN
  IF (TOKEN_ = 'CREATE') THEN
    insert into avion(idAvion, marcaAvion, modeloAvion, capacidadAvion,observacionesAvion) values(ID_, MARCA_, MODELO_, CAPAC_, OBSER_);
    COMMIT;
    RETURN 'TRUE';
  ELSIF(TOKEN_ = 'MODIFY') THEN
    update avion set avion.observacionesAvion=OBSER_ where avion.idAvion = ID_;
    COMMIT;
    RETURN 'TRUE';
  END IF;
  EXCEPTION
    WHEN OTHERS THEN
    ROLLBACK;
    RETURN 'FALSE';
END;
/


create or replace
function RECORD_RUTA(TOKEN_ varchar2, ID_ ruta.idruta%TYPE, ID_AV_ ruta.idAvion%TYPE, CI_P_ ruta.cipiloto%TYPE, DESTINO_ ruta.destinoRuta%TYPE, FECHA_ ruta.FECHAHORARUTA%TYPE, ANDEN_ ruta.andenRuta%TYPE, PRECIO_ ruta.precioRuta%TYPE) return varchar2
as
begin
  if(TOKEN_ = 'CREATE')THEN
    insert into ruta(idRuta,idAvion,ciPiloto,destinoRuta,fechaHoraRuta,andenRuta,lugaresDisponiblesRuta,precioRuta)
                  values(ID_,ID_AV_,CI_P_,DESTINO_,FECHA_,ANDEN_,(select avion.capacidadAvion from avion where avion.idAvion = ID_AV_),PRECIO_);
    commit;
    return 'TRUE';
  elsif(TOKEN_ = 'MODIFY')THEN
    update ruta set ruta.andenRuta = ANDEN_, ruta.precioRuta = PRECIO_ where ruta.idRuta = ID_;
    commit;
    return 'TRUE';
  end if;
  exception
    when others then
      rollback;
      return SQLERRM;
end;
/



commit;










create table EVENTO(
  IDEVENTO NUMERIC PRIMARY KEY NOT NULL,
  FECHAEVENTO DATE NOT NULL,
  DESCRIPCIONEVENTO VARCHAR2(1000) NOT NULL
);


CREATE OR REPLACE TRIGGER monitor_rutas
  BEFORE INSERT or delete or update ON ruta for each row
DECLARE
BEGIN
    escribir_evento(concat('Ruta insertada, eliminada o actualizada: ', :new.IDRUTA));
END;
/


delete from evento;
commit;

select * from evento;


create or replace
procedure escribir_evento(descripcion evento.descripcionevento%TYPE)
as
  id_evento evento.idevento%TYPE;
begin
  select count(evento.idevento) into id_evento from evento;
  insert into evento(IDEVENTO,  FECHAEVENTO, DESCRIPCIONEVENTO) values(id_evento, sysdate, descripcion);
  
end;
/



create or replace
function RECORD_CLIENTE(CI_ cliente.cicliente%type, NOM_ cliente.nombrecliente%TYPE, APP_ cliente.apellidocliente%type, MAIL_ cliente.mailcliente%type, PASS_ cliente.passwordcliente%TYPE) return varchar2
as
  existe_cliente numeric;
begin
  select count(*) into existe_cliente from cliente where cliente.CICLIENTE = CI_ or cliente.MAILCLIENTE = MAIL_;
  IF (existe_cliente = 0)then
    insert into cliente(CICLIENTE, NOMBRECLIENTE, APELLIDOCLIENTE, MAILCLIENTE, PASSWORDCLIENTE)
    VALUES (CI_, NOM_, APP_, MAIL_, PASS_);
    COMMIT;
    RETURN 'TRUE';
  ELSE
    RETURN 'El Numero de CI o el e-mail ya han sido registrados..!';
  END IF;
  exception
  when others then
    ROLLBACK;
    return SQLERRM;
end;
/


create or replace
function logincliente(ID_ varchar2, PASS_ cliente.passwordcliente%TYPE)return varchar2
as
  login_ numeric;
begin
  select count(*) into login_ from cliente cli where cli.CICLIENTE = ID_ and cli.passwordcliente = PASS_;
  if (login_ = 1)then
    return 'TRUE';
  else
    select count(*) into login_ from cliente cli where cli.MAILCLIENTE = ID_ and cli.passwordcliente = PASS_;
    if (login_ = 1)then
      return 'TRUE';
    else
      return 'FALSE';
    end if;
  end if;
  exception
  when others then
    return 'FALSE';
end;
/










create or replace
function crearReservacion(id_ruta ruta.idruta%type,id_cliente varchar2, num_asientos ruta.lugaresDisponiblesRuta%type) return varchar2
as
  id_cliente_ cliente.ciCliente%type;
  id_reservacion reservacion.idreservacion%type;
  lugares_actuales ruta.lugaresdisponiblesruta%type;
  nuevos_lugares_disponibles ruta.lugaresdisponiblesruta%type;
begin
  select ruta.lugaresDisponiblesRuta into lugares_actuales from ruta where ruta.idRuta = id_ruta;
        
        if (lugares_actuales >= num_asientos) then            
            select count(*) into id_reservacion from reservacion;            
            insert into  reservacion(idReservacion, idRuta, idAvion, ciPiloto, ciCliente, fechaReservacion, asientoReservacion, estadoReservacion)
            values( id_reservacion,
                    id_ruta,
                    (select ruta.idAvion from ruta where ruta.idRuta = id_ruta),
                    (select ruta.ciPiloto from ruta where ruta.idRuta = id_ruta),
                    (select cliente.cicliente from cliente where cliente.cicliente = id_cliente or cliente.mailcliente = id_cliente),
                    sysdate,
                    lugares_actuales,
                    'No facturada');
            
            nuevos_lugares_disponibles := lugares_actuales - num_asientos;
            update ruta set ruta.lugaresDisponiblesRuta = nuevos_lugares_disponibles where ruta.idRuta = id_ruta;
            commit;
            return id_reservacion;
        else
          return 'FALSE';
        end if;
        exception
        when others then
          return 'FALSE';
    end;
/


create or replace
function RECORD_FACTURA(id_reservacion factura.idreservacion%type,codTicketBank factura.TICKETDEPOSITOFACTURACION%type) return varchar2
as
begin
      insert into factura(idFactura, idReservacion, idRuta, idAvion, ciPiloto, ciCliente, fechaFacturacion, ticketDepositoFacturacion)
        values( id_reservacion,
                id_reservacion,
                (select reservacion.idRuta from reservacion where reservacion.idReservacion = id_reservacion),
                (select reservacion.idAvion from reservacion where reservacion.idReservacion = id_reservacion),
                (select reservacion.ciPiloto from reservacion where reservacion.idReservacion = id_reservacion),
                (select reservacion.ciCliente from reservacion where reservacion.idReservacion = id_reservacion),
                sysdate,
                codTicketBank );
        update reservacion set reservacion.estadoReservacion='Facturado' where reservacion.idReservacion = id_reservacion;
        commit;
    return 'TRUE';
    exception
      when others then
        return 'FALSE';
end;
/

commit;


commit;

create or replace
procedure actualizarReservaciones
as
  self_id numeric;
  id_reservacion_ reservacion.idreservacion%TYPE;
  fecha_salida_ ruta.FECHAHORARUTA%type;
  count_ numeric;
  cursor actualizacion is select res.idreservacion, rta.FECHAHORARUTA from reservacion res , ruta rta where res.idruta = rta.idruta and res.estadoreservacion = 'No facturada' and rta.FECHAHORARUTA < sysdate;
begin
  open actualizacion;
  loop
    fetch actualizacion into id_reservacion_, fecha_salida_;
    exit when actualizacion%NOTFOUND;
    select count(*) into count_ from reservacionIncumplida where reservacionIncumplida.ID_RESERVACION_INCUMPLIDA = id_reservacion_;
    if(count_ = 1) then
      update reservacionIncumplida set TIEMPO_RETARDO=substr(to_char(round(to_number(sysdate - fecha_salida_), 0)), 0, 10) where reservacionIncumplida.ID_RESERVACION_INCUMPLIDA = id_reservacion_;
    else
      select count(*) into self_id from reservacionIncumplida;
      insert into reservacionIncumplida(ID_, ID_RESERVACION_INCUMPLIDA, TIEMPO_RETARDO) values (self_id,id_reservacion_,substr(to_char(round(to_number(sysdate - fecha_salida_), 0)), 0, 10));
    end if;
  end loop;
  close actualizacion;
  COMMIT;
  EXCEPTION
    WHEN OTHERS THEN
    ROLLBACK;
end;
/

commit;

begin
actualizarReservaciones;
end;
/
select * from evento;
drop table ptab;