import React, { useState } from 'react';
import {
  Box,
  VStack,
  HStack,
  Text,
  Input,
  Button,
  Alert,
  useToast,
  Spinner,
  Center,
  Image,
  Heading,
  FormControl,
  WarningOutlineIcon,
} from 'native-base';
import { Ionicons } from '@expo/vector-icons';
import { useForm, Controller } from 'react-hook-form';
import { useAuth } from '../../hooks/useAuth';

interface LoginForm {
  username: string;
  password: string;
}

export default function LoginScreen() {
  const { login } = useAuth();
  const toast = useToast();
  const [loading, setLoading] = useState(false);
  
  const {
    control,
    handleSubmit,
    formState: { errors },
  } = useForm<LoginForm>();

  const onSubmit = async (data: LoginForm) => {
    try {
      setLoading(true);
      await login(data.username, data.password);
      
      toast.show({
        title: 'Login realizado com sucesso!',
        status: 'success',
      });
    } catch (error: any) {
      toast.show({
        title: 'Erro no login',
        description: error.message || 'Credenciais inválidas',
        status: 'error',
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <Box flex={1} bg="white" safeArea>
      <VStack flex={1} px={6} justifyContent="center" space={8}>
        {/* Logo/Header */}
        <Center>
          <Box
            w={120}
            h={120}
            bg="blue.500"
            borderRadius="full"
            justifyContent="center"
            alignItems="center"
            mb={4}
          >
            <Ionicons name="warning" size={60} color="white" />
          </Box>
          <Heading size="xl" color="gray.800" mb={2}>
            Sistema de Forcing
          </Heading>
          <Text color="gray.600" textAlign="center">
            Controle operacional de forcing
          </Text>
        </Center>

        {/* Formulário */}
        <VStack space={4}>
          <FormControl isInvalid={!!errors.username}>
            <FormControl.Label>Username</FormControl.Label>
            <Controller
              control={control}
              name="username"
              rules={{ required: 'Username é obrigatório' }}
              render={({ field: { onChange, value } }) => (
                <Input
                  placeholder="Digite seu username"
                  value={value}
                  onChangeText={onChange}
                  InputLeftElement={
                    <Ionicons name="person" size={20} color="gray" style={{ marginLeft: 12 }} />
                  }
                />
              )}
            />
            <FormControl.ErrorMessage leftIcon={<WarningOutlineIcon size="xs" />}>
              {errors.username?.message}
            </FormControl.ErrorMessage>
          </FormControl>

          <FormControl isInvalid={!!errors.password}>
            <FormControl.Label>Senha</FormControl.Label>
            <Controller
              control={control}
              name="password"
              rules={{ required: 'Senha é obrigatória' }}
              render={({ field: { onChange, value } }) => (
                <Input
                  placeholder="Digite sua senha"
                  value={value}
                  onChangeText={onChange}
                  type="password"
                  secureTextEntry
                  InputLeftElement={
                    <Ionicons name="lock-closed" size={20} color="gray" style={{ marginLeft: 12 }} />
                  }
                />
              )}
            />
            <FormControl.ErrorMessage leftIcon={<WarningOutlineIcon size="xs" />}>
              {errors.password?.message}
            </FormControl.ErrorMessage>
          </FormControl>

          <Button
            onPress={handleSubmit(onSubmit)}
            isLoading={loading}
            isLoadingText="Entrando..."
            bg="blue.500"
            _pressed={{ bg: 'blue.600' }}
            size="lg"
            mt={4}
          >
            Entrar
          </Button>
        </VStack>

        {/* Footer */}
        <Center>
          <Text color="gray.500" fontSize="sm">
            Versão 1.0.0
          </Text>
        </Center>
      </VStack>
    </Box>
  );
}

